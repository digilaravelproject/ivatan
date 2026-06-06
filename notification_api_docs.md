# Notification System — API Documentation for Flutter

> **Version**: 1.0  
> **Last Updated**: 2026-06-06  
> **Stack**: Laravel 12 + Reverb (WebSocket) + Firebase Cloud Messaging (Push)

---

## Table of Contents

1. [System Architecture Overview](#1-system-architecture-overview)
2. [Notification Data Model](#2-notification-data-model)
3. [API Endpoints](#3-api-endpoints)
   - [3.1 List Notifications](#31-list-notifications)
   - [3.2 Unread Count](#32-unread-count)
   - [3.3 Mark as Read](#33-mark-as-read)
   - [3.4 Mark All as Read](#34-mark-all-as-read)
   - [3.5 Register Device Token (FCM)](#35-register-device-token-fcm)
   - [3.6 Delete Device Token](#36-delete-device-token)
   - [3.7 Send Test Notification (Debug)](#37-send-test-notification-debug)
4. [Real-Time WebSocket Events (Reverb)](#4-real-time-websocket-events-reverb)
   - [4.1 Notification Received Event](#41-notification-received-event)
   - [4.2 Chat Events](#42-chat-events)
5. [Firebase Cloud Messaging (Push) Integration](#5-firebase-cloud-messaging-push-integration)
   - [5.1 Setup in Flutter](#51-setup-in-flutter)
   - [5.2 Registering Device Tokens](#52-registering-device-tokens)
   - [5.3 Handling Incoming Push Notifications](#53-handling-incoming-push-notifications)
   - [5.4 Push Notification Payload Structure](#54-push-notification-payload-structure)
6. [Notification Categories](#6-notification-categories)
7. [Unread Count Caching System](#7-unread-count-caching-system)
8. [Error Handling Patterns](#8-error-handling-patterns)
9. [Flutter Integration Checklist](#9-flutter-integration-checklist)

---

## 1. System Architecture Overview

```
┌──────────────────┐       ┌─────────────────────────────────────┐
│   Flutter App    │       │           Laravel Backend            │
│                  │       │                                      │
│  ┌────────────┐  │ REST  │  ┌────────────┐  ┌───────────────┐  │
│  │ API Client │──┼───────┼─►│Controllers │─►│NotificationSvc│  │
│  └────────────┘  │       │  └────────────┘  └───────┬───────┘  │
│                  │       │                          │          │
│  ┌────────────┐  │       │              ┌───────────┼──────────┐│
│  │ Laravel    │  │       │              ▼           ▼          ││
│  │ Echo/WSS   │──┼───────┼─►    ┌──────────┐ ┌──────────┐     ││
│  │ (Reverb)   │  │       │      │ Database │ │ Broadcast│     ││
│  └────────────┘  │       │      │ (Persist)│ │ (Reverb) │     ││
│                  │       │      └──────────┘ └─────┬────┘     ││
│  ┌────────────┐  │       │              ┌──────────┼──────────┐│
│  │ Firebase   │◄─┼───────┼─►    ┌──────────┐       │         ││
│  │ Cloud Msg  │  │       │      │ FCM Push │       │         ││
│  └────────────┘  │       │      │ (Firebase)│       │         ││
│                  │       │      └──────────┘       │         ││
└──────────────────┘       └─────────────────────────────────────┘
```

### Three Delivery Channels

| Channel | When | Where | Technology |
|---------|------|-------|------------|
| **Database** | Always | Persistent notification history | Laravel `notifications` table |
| **Broadcast (Reverb)** | Real-time while app is foregrounded | In-app notification badge | Laravel Reverb (WebSocket) |
| **Push (FCM)** | When app is backgrounded or closed | Phone notification tray | Firebase Cloud Messaging |

### Flow for Sending a Notification

1. Any action (like, comment, follow, order, etc.) calls `NotificationService::sendToUser()`
2. The service creates a `GenericNotification` and sends it through the configured channels
3. **Database channel**: Saves to `notifications` table + updates unread count cache
4. **Broadcast channel**: Delivers real-time via Reverb WebSocket
5. **FCM channel**: Sends push notification via Firebase (only for users with registered device tokens)

---

## 2. Notification Data Model

### Database Record (`notifications` table)

```json
{
  "id": "uuid-string",
  "type": "App\\Notifications\\GenericNotification",
  "notifiable_type": "App\\Models\\User",
  "notifiable_id": 1,
  "data": {
    "category": "like",
    "payload": {
      "title": "New Like",
      "message": "John liked your post",
      "actor_id": 5,
      "actor_name": "John",
      "actor_avatar": "https://example.com/avatar.jpg",
      "target_type": "App\\Models\\UserPost",
      "target_id": 42,
      "action_url": null
    },
    "sent_at": "2026-06-04T10:30:00.000000Z"
  },
  "read_at": null,
  "created_at": "2026-06-04T10:30:00.000000Z",
  "updated_at": "2026-06-04T10:30:00.000000Z"
}
```

### API Response Shape (List/Index)

The `index()` endpoint returns a paginated response with the following structure:

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "type": "App\\Notifications\\GenericNotification",
        "data": {
          "category": "like",
          "payload": {
            "title": "New Like",
            "message": "John liked your post",
            "actor_id": 5,
            "actor_name": "John",
            "actor_avatar": "https://example.com/avatar.jpg",
            "target_type": "App\\Models\\UserPost",
            "target_id": 42,
            "action_url": null
          },
          "sent_at": "2026-06-04T10:30:00.000000Z"
        },
        "read_at": null,
        "created_at": "2026-06-04T10:30:00.000000Z"
      }
    ],
    "first_page_url": "https://your-domain.com/api/v1/notifications?page=1",
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 100
  }
}
```

> **Note**: The `data` field in each notification item contains the raw `toArray()` output from `GenericNotification`:
> - `category` — notification category (e.g., `like`, `comment`, `follow`)
> - `payload` — custom payload passed when sending the notification
> - `sent_at` — ISO timestamp when notification was sent
> - The `type`, `read_at`, `created_at` fields are from the base `notifications` table columns

### API Response Shape (List/Index)

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "uuid-string",
        "type": "App\\Notifications\\GenericNotification",
        "data": {
          "category": "like",
          "payload": {
            "title": "New Like",
            "message": "John liked your post",
            "actor_id": 5,
            "actor_name": "John",
            "actor_avatar": "https://example.com/avatar.jpg",
            "target_type": "App\\Models\\UserPost",
            "target_id": 42,
            "action_url": null
          },
          "sent_at": "2026-06-04T10:30:00.000000Z"
        },
        "read_at": null,
        "created_at": "2026-06-04T10:30:00.000000Z"
      }
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 100
  }
}
```

---

## 3. API Endpoints

All notification endpoints require **Bearer token authentication** (Sanctum) and are prefixed with `/api/v1`.

**Headers:**
```
Authorization: Bearer <sanctum_token>
Accept: application/json
```

**Base URL:** `https://your-domain.com/api/v1`

---

### 3.1 List Notifications

```
GET /api/v1/notifications
```

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `only` | string | `all` | Filter: `all` or `unread` |
| `per_page` | integer | `20` | Items per page |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "type": "App\\Notifications\\GenericNotification",
        "data": {
          "category": "like",
          "payload": {
            "title": "New Like",
            "message": "John liked your post",
            "actor_id": 5,
            "actor_name": "John",
            "actor_avatar": "https://example.com/avatar.jpg",
            "target_type": "App\\Models\\UserPost",
            "target_id": 42,
            "action_url": null
          },
          "sent_at": "2026-06-04T10:30:00.000000Z"
        },
        "read_at": null,
        "created_at": "2026-06-04T10:30:00.000000Z"
      }
    ],
    "first_page_url": "https://your-domain.com/api/v1/notifications?page=1",
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 100
  }
}
```

**Flutter Usage:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/notifications?only=unread&per_page=20'),
  headers: {'Authorization': 'Bearer $token'},
);
final data = jsonDecode(response.body);
```

---

### 3.2 Unread Count

```
GET /api/v1/notifications/unread-count
```

**Response `200 OK`:**

```json
{
  "success": true,
  "unread": 5
}
```

**Flutter Usage:**
```dart
// Use this to show a badge count on the notification icon
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/notifications/unread-count'),
  headers: {'Authorization': 'Bearer $token'},
);
final body = jsonDecode(response.body);
if (body['success'] == true) {
  final count = body['unread'] as int;
  // badgeIcon.show(count);
}
```

---

### 3.3 Mark as Read

```
POST /api/v1/notifications/mark-read
Content-Type: application/json
```

**Request Body:**

```json
{
  "notification_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Response `200 OK`:**

```json
{
  "success": true
}
```

**Error Response (Notification Not Found):**
Returns a **500 Internal Server Error** (throws `ModelNotFoundException`) since the controller uses `throw new ModelNotFoundException('Notification not found.')` which is not caught locally.

```json
{
  "success": false,
  "message": "Notification not found."
}
```

> **Note**: The `mark-read` endpoint does not return a 404. It throws an exception that results in a 500 response. Consider catching this on the Flutter side or the backend should be updated to return 404.

**Flutter Usage:**
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/v1/notifications/mark-read'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({'notification_id': notificationId}),
);
final body = jsonDecode(response.body);
if (body['success'] == true) {
  // Marked read successfully
} else if (response.statusCode == 500) {
  // Notification not found (returns 500, not 404)
  // Handle error
}
```

---

### 3.4 Mark All as Read

```
POST /api/v1/notifications/mark-all-read
```

**Response `200 OK`:**

```json
{
  "success": true
}
```

**Flutter Usage (e.g., on notification screen open):**
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/v1/notifications/mark-all-read'),
  headers: {'Authorization': 'Bearer $token'},
);
final body = jsonDecode(response.body);
if (body['success'] == true) {
  // All marked read
}
```

---

### 3.5 Register Device Token (FCM)

```
POST /api/v1/notifications/device-tokens
Content-Type: application/json
```

Call this immediately after getting the FCM token from Firebase (on app launch, and whenever the token refreshes).

**Request Body:**

```json
{
  "token": "fQ5t7...abc123:APA91bG...firebase-device-token",
  "device": "android"
}
```

| Field | Type | Required | Values |
|-------|------|----------|--------|
| `token` | string | **yes** | FCM device token (max 500 chars) |
| `device` | string | no | `ios`, `android`, or `web` |

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Device token registered."
}
```

**Flutter Usage:**
```dart
import 'package:firebase_messaging/firebase_messaging.dart';

final fcm = FirebaseMessaging.instance;
final fcmToken = await fcm.getToken();

final response = await http.post(
  Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'token': fcmToken,
    'device': Platform.isAndroid ? 'android' : 'ios',
  }),
);
final body = jsonDecode(response.body);
if (body['success'] == true) {
  // Token registered
}

// Listen for token refresh
fcm.onTokenRefresh.listen((newToken) {
  // Re-register with the same endpoint
});
```

---

### 3.6 Delete Device Token

```
DELETE /api/v1/notifications/device-tokens
Content-Type: application/json
```

Call this on user logout to stop receiving push notifications.

**Request Body:**

```json
{
  "token": "fQ5t7...abc123:APA91bG...firebase-device-token"
}
```

**Response `200 OK`:**

```json
{
  "success": true,
  "message": "Device token removed."
}
```

**Flutter Usage (on logout):**
```dart
final fcm = FirebaseMessaging.instance;
final fcmToken = await fcm.getToken();

final response = await http.delete(
  Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({'token': fcmToken}),
);
final body = jsonDecode(response.body);
if (body['success'] == true) {
  // Token removed
}
```

---

### 3.7 Send Test Notification (Debug)

```
POST /api/v1/notifications/send-test
Content-Type: application/json
```

For development/testing only.

**Request Body:**

```json
{
  "user_id": 1,
  "category": "like",
  "payload": {
    "title": "Test Notification",
    "message": "This is a test message"
  }
}
```

**Response `200 OK`:**

```json
{
  "success": true
}
```

---

## 4. Real-Time WebSocket Events (Reverb)

### 4.1 Notification Received Event

When a notification is sent, it broadcasts on a **private channel**:

**Channel:** `App.Models.User.{user_id}`

**Event:** `Illuminate\Notifications\Events\BroadcastNotificationCreated`

**Payload** (from `GenericNotification::toBroadcast()`):

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "category": "like",
  "payload": {
    "title": "New Like",
    "message": "John liked your post",
    "actor_id": 5,
    "actor_name": "John",
    "actor_avatar": "https://example.com/avatar.jpg",
    "target_type": "App\\Models\\UserPost",
    "target_id": 42,
    "action_url": null
  },
  "notifiable_id": 1,
  "sent_at": "2026-06-04T10:30:00.000000Z"
}
```

> **Note**: Unlike the database record, the broadcast payload does **NOT** include `type` (the FQCN). It uses a generated UUID for `id` and includes `category`, `payload`, `notifiable_id`, and `sent_at`.

**Flutter Setup with Laravel Echo:**

```dart
import 'package:laravel_echo/laravel_echo.dart';
import 'package:pusher_client/pusher_client.dart';

final echo = Echo({
  'broadcaster': 'pusher',
  'key': 'your-reverb-app-key',
  'cluster': null,
  'wsHost': 'your-domain.com',
  'wsPort': 443,
  'wssPort': 443,
  'forceTLS': true,
  'disableStats': true,
  'authEndpoint': '$baseUrl/broadcasting/auth',
  'auth': {
    'headers': {
      'Authorization': 'Bearer $sanctumToken',
    },
  },
});

// Listen for new notifications
echo.private('App.Models.User.$userId')
  .notification((notification) {
    // This fires when ANY notification is broadcast
    print('New notification: ${notification.category}');
    // Update UI — increment badge, show snackbar, etc.
  });
```

> **Note for Flutter**: Using `laravel_echo` with `pusher_client` on Flutter requires the Pusher protocol. Reverb is fully Pusher-compatible, so the same client libraries work.

---

### 4.2 Chat Events

Chat events broadcast on a **private channel**:

**Channel:** `chat.{chat_id}`

#### Message Sent

**Event name (client-side):** `message.sent`

```json
{
  "id": 1,
  "chat_id": 5,
  "sender_id": 2,
  "content": "Hello!",
  "message_type": "text",
  "attachment_path": null,
  "meta": null,
  "reply_to_message_id": null,
  "delivered_at": "2026-06-04T10:30:00.000000Z",
  "created_at": "2026-06-04T10:30:00.000000Z",
  "sender": {
    "id": 2,
    "name": "John",
    "profile_photo_url": "https://..."
  }
}
```

**Flutter Listener:**
```dart
echo.private('chat.$chatId')
  .listen('message.sent', (event) {
    // Add message to chat UI
  });
```

#### Message Read

**Event name (client-side):** `message.read`

```json
{
  "chat_id": 5,
  "user_id": 2,
  "last_read_message_id": 42
}
```

**Flutter Listener:**
```dart
echo.private('chat.$chatId')
  .listen('message.read', (event) {
    // Update read receipts
  });
```

---

## 5. Firebase Cloud Messaging (Push) Integration

### 5.1 Setup in Flutter

**pubspec.yaml:**
```yaml
dependencies:
  firebase_core: ^2.x
  firebase_messaging: ^14.x
  flutter_local_notifications: ^15.x  # for displaying notifications in foreground
```

**Initialization (in main.dart):**
```dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);
  
  // Request permissions (iOS)
  final messaging = FirebaseMessaging.instance;
  await messaging.requestPermission(
    alert: true,
    badge: true,
    sound: true,
  );
  
  runApp(const MyApp());
}

// Global handler for background messages
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  // Handle background notification
  // e.g., update local notification badge
}
```

### 5.2 Registering Device Tokens

Call this on app start and on token refresh:

```dart
class NotificationService {
  static Future<void> registerFcmToken(String apiToken) async {
    final fcm = FirebaseMessaging.instance;
    
    // Get initial token
    final token = await fcm.getToken();
    if (token != null) {
      await _sendTokenToServer(token, apiToken);
    }
    
    // Listen for token refresh
    fcm.onTokenRefresh.listen((newToken) {
      _sendTokenToServer(newToken, apiToken);
    });
  }
  
  static Future<void> _sendTokenToServer(String fcmToken, String apiToken) async {
    await http.post(
      Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
      headers: {
        'Authorization': 'Bearer $apiToken',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'token': fcmToken,
        'device': Platform.isAndroid ? 'android' : 'ios',
      }),
    );
  }
  
  static Future<void> unregisterFcmToken(String apiToken) async {
    final fcm = FirebaseMessaging.instance;
    final token = await fcm.getToken();
    if (token != null) {
      await http.delete(
        Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
        headers: {
          'Authorization': 'Bearer $apiToken',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({'token': token}),
      );
    }
  }
}
```

### 5.3 Handling Incoming Push Notifications

```dart
class PushHandler {
  static Future<void> initialize() async {
    final messaging = FirebaseMessaging.instance;
    
    // Foreground messages
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      _showLocalNotification(message);
    });
    
    // When app opened from background notification
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      _navigateToNotification(message);
    });
    
    // When app opened from terminated state via notification
    final initialMessage = await messaging.getInitialMessage();
    if (initialMessage != null) {
      _navigateToNotification(initialMessage);
    }
  }
  
  static void _showLocalNotification(RemoteMessage message) {
    final notification = message.notification;
    final data = message.data;
    
    final fln = FlutterLocalNotificationsPlugin();
    const androidDetails = AndroidNotificationDetails(
      'notifications_channel',
      'Notifications',
      channelDescription: 'App notifications',
      importance: Importance.high,
      priority: Priority.high,
    );
    const iosDetails = DarwinNotificationDetails();
    
    fln.show(
      notification.hashCode,
      notification?.title ?? data['title'] ?? 'Notification',
      notification?.body ?? data['body'] ?? '',
      const NotificationDetails(
        android: androidDetails,
        iOS: iosDetails,
      ),
      payload: jsonEncode(data),
    );
  }
  
  static void _navigateToNotification(RemoteMessage message) {
    final data = message.data;
    final category = data['category'];
    
    // Navigate based on notification category
    switch (category) {
      case 'chat_message':
        // Navigate to chat screen
        break;
      case 'like':
      case 'comment':
        // Navigate to post
        break;
      case 'new_order':
        // Navigate to order
        break;
      default:
        // Navigate to notifications list
        break;
    }
  }
}
```

### 5.4 Push Notification Payload Structure

When FCM delivers a push, it includes:

```
Notification payload (visible in system tray):
  title: "New Like"
  body: "John liked your post"

Data payload (accessible from app code):
  category: "like"
  payload: "{\"title\":\"New Like\",\"message\":\"John liked your post\",...}"
  sent_at: "2026-06-04T10:30:00.000000Z"
  click_action: "FLUTTER_NOTIFICATION_CLICK"
```

The `category` field in the data payload is the key to determining what screen to navigate to.

---

## 6. Notification Categories

All notification categories defined in `config/notifications.php`:

| Category | Push Enabled | Default Channels | Description |
|----------|-------------|------------------|-------------|
| `like` | ✅ Yes | `database`, `broadcast` | Someone likes your post/reel |
| `comment` | ✅ Yes | `database`, `broadcast` | Someone comments on your post |
| `follow` | ✅ Yes | `database`, `broadcast` | Someone follows you |
| `new_order` | ✅ Yes | `database`, `broadcast` | Customer places an order (to seller) |
| `payment_success` | ✅ Yes | `database`, `broadcast` | Payment completed successfully |
| `order_status` | ✅ Yes | `database`, `broadcast` | Seller updates order status |
| `order_cancelled` | ✅ Yes | `database`, `broadcast` | Order is cancelled |
| `admin_action` | ✅ Yes | `database`, `broadcast` | Admin blocks/unblocks/verifies user |
| `chat_message` | ✅ Yes | `database`, `broadcast` | New chat message received |
| `custom` | ❌ No | `database`, `broadcast` | Admin sends direct message |
| `broadcast` | ✅ Yes | `database`, `broadcast` | Admin broadcast to all users |
| `post_flagged` | ✅ Yes | `database`, `broadcast` | Admin flags/deletes your post |
| `welcome` | ✅ Yes | `database`, `broadcast` | User registration welcome |
| `enquiry_update` | ✅ Yes | `database`, `broadcast` | Seller replies to your enquiry |
| `content_approved` | ✅ Yes | `database`, `broadcast` | Admin approves product/service/ad |
| `content_rejected` | ✅ Yes | `database`, `broadcast` | Admin rejects product/service/ad |

> **Note**: The `push` column indicates if FCM push is enabled for that category (controlled by `config/notifications.php`). Categories with `push: false` will not send FCM push notifications even if the user has registered device tokens.

---

## 7. Unread Count System (Hybrid Approach)

The system uses a **hybrid approach** for unread counts:

### How It Works

| Operation | Method | Description |
|-----------|--------|-------------|
| **Get unread count** | `unreadCount()` | Queries `$user->unreadNotifications()->count()` directly from DB (no cache) |
| **Mark as read** | `markRead()` | Marks notification read, then **decrements** `notification_unread_counts` cache table |
| **Mark all read** | `markAllRead()` | Marks all read, then **resets** `notification_unread_counts` to 0 |
| **On notification sent** | `NotificationService::sendToUser()` | Listener **increments** `notification_unread_counts` cache |

### Why Hybrid?
- **Read path** (unreadCount): Direct DB query ensures accuracy — no stale cache
- **Write paths** (mark-read, mark-all-read, send): Update cache table for potential future use or admin dashboards

### Flutter Badge Strategy

```dart
// On app start or screen focus, sync badge from server
int badgeCount = await _fetchUnreadCount();

// On receiving a Reverb notification event, increment locally
echo.private('App.Models.User.$userId')
  .notification((_) {
    badgeCount++;
    _updateBadge(badgeCount);
  });

// On marking as read, decrement locally
void markAsRead(String notificationId) async {
  await _apiMarkRead(notificationId);
  badgeCount = (badgeCount - 1).clamp(0, badgeCount);
  _updateBadge(badgeCount);
}

// On mark-all, reset to 0
void markAllAsRead() async {
  await _apiMarkAllRead();
  badgeCount = 0;
  _updateBadge(0);
}
```

> **Important**: Since `unreadCount()` queries the DB directly, it always returns the correct count. The cache table is a write optimization and should not be relied upon for reads.

---

## 8. Error Handling Patterns

### API Error Responses

All notification endpoints follow a consistent error format:

| Status Code | Meaning |
|-------------|---------|
| `200` | Success |
| `401` | Unauthenticated (missing/invalid token) |
| `404` | Resource not found |
| `422` | Validation error |
| `500` | Server error |

**Validation Error Response `422`:**

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "notification_id": ["The notification id field is required."],
    "token": ["The token field is required."]
  }
}
```

**Server Error Response `500`:**

```json
{
  "success": false,
  "message": "An internal server error occurred."
}
```

### Flutter Error Handling Template

```dart
Future<Map<String, dynamic>> _apiCall(Future<http.Response> Function() call) async {
  try {
    final response = await call();
    final body = jsonDecode(response.body);
    
    if (response.statusCode == 200) {
      if (body['success'] == true) {
        return body;
      }
      throw ApiException(body['message'] ?? 'Request failed');
    }
    
    if (response.statusCode == 422) {
      throw ValidationException(body['errors'] ?? {});
    }
    
    throw ApiException(body['message'] ?? 'Server error');
  } on SocketException {
    throw ApiException('No internet connection');
  } on TimeoutException {
    throw ApiException('Request timed out');
  }
}
```

---

## 9. Flutter Integration Checklist

### Initial Setup

- [ ] Add `firebase_core`, `firebase_messaging` to `pubspec.yaml`
- [ ] Add `flutter_local_notifications` for foreground display
- [ ] Add `laravel_echo` and `pusher_client` for Reverb WebSocket
- [ ] Download `google-services.json` (Android) and `GoogleService-Info.plist` (iOS) from Firebase Console
- [ ] Run `flutterfire configure` or manually configure Firebase

### App Startup (on every launch)

1. [ ] Initialize Firebase: `Firebase.initializeApp()`
2. [ ] Request notification permissions (iOS)
3. [ ] Get FCM token and call `POST /api/v1/notifications/device-tokens`
4. [ ] Set up FCM token refresh listener: `firebaseMessaging.onTokenRefresh`
5. [ ] Set up foreground message handler: `FirebaseMessaging.onMessage`
6. [ ] Set up background message handler: `FirebaseMessaging.onMessageOpenedApp`
7. [ ] Connect to Reverb WebSocket via Laravel Echo
8. [ ] Subscribe to private channel: `echo.private('App.Models.User.$userId')`
9. [ ] Listen for `.notification` events to update badge in real-time
10. [ ] Fetch initial unread count: `GET /api/v1/notifications/unread-count`

### On Logout

1. [ ] Call `DELETE /api/v1/notifications/device-tokens` with current FCM token
2. [ ] Disconnect Reverb WebSocket connection
3. [ ] Clear local notification state

### Notification Screen

- [ ] Call `GET /api/v1/notifications` with pagination (20 per page)
- [ ] Show `data.data[].data.payload.title` and `data.data[].data.payload.message`
- [ ] Show `data.data[].created_at` as relative time
- [ ] Show unread indicator when `data.data[].read_at` is null
- [ ] On tap: call `POST /api/v1/notifications/mark-read` and navigate based on `category`
- [ ] Pull-to-refresh support
- [ ] Infinite scroll (pagination)

### Navigation Mapping

| Category | Navigate To |
|----------|-------------|
| `like`, `comment` | Post screen (`target_type`, `target_id`) |
| `follow` | Profile screen (`actor_id`) |
| `new_order`, `order_status`, `order_cancelled` | Order detail screen |
| `chat_message` | Chat screen (`chat_id`) |
| `admin_action` | Profile/verification screen |
| `content_approved`, `content_rejected` | My products/services listing |
| `post_flagged` | Post screen |
| `enquiry_update` | Enquiry detail screen |
| `welcome`, `broadcast`, `custom` | Notifications list screen |

---

## Quick Reference: All Endpoints

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| `GET` | `/api/v1/notifications` | ✅ Bearer | List notifications (paginated) |
| `GET` | `/api/v1/notifications/unread-count` | ✅ Bearer | Get unread badge count |
| `POST` | `/api/v1/notifications/mark-read` | ✅ Bearer | Mark single as read |
| `POST` | `/api/v1/notifications/mark-all-read` | ✅ Bearer | Mark all as read |
| `POST` | `/api/v1/notifications/send-test` | ✅ Bearer | Debug: send test notification |
| `POST` | `/api/v1/notifications/device-tokens` | ✅ Bearer | Register FCM device token |
| `DELETE` | `/api/v1/notifications/device-tokens` | ✅ Bearer | Remove FCM device token |
