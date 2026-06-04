# Notification System — API Documentation for Flutter

> **Version**: 1.0  
> **Last Updated**: 2026-06-04  
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

All notification endpoints require **Bearer token authentication** (Sanctum).

**Headers:**
```
Authorization: Bearer <sanctum_token>
Accept: application/json
```

---

### 3.1 List Notifications

```
GET /api/notifications
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
    "per_page": 20,
    "total": 100
  }
}
```

**Flutter Usage:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/notifications?only=unread&per_page=20'),
  headers: {'Authorization': 'Bearer $token'},
);
final data = jsonDecode(response.body);
```

---

### 3.2 Unread Count

```
GET /api/notifications/unread-count
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
  Uri.parse('$baseUrl/api/notifications/unread-count'),
  headers: {'Authorization': 'Bearer $token'},
);
final count = jsonDecode(response.body)['unread'];
// badgeIcon.show(count);
```

---

### 3.3 Mark as Read

```
POST /api/notifications/mark-read
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

**Error Response `404`:**

```json
{
  "message": "Notification not found."
}
```

**Flutter Usage:**
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/notifications/mark-read'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({'notification_id': notificationId}),
);
```

---

### 3.4 Mark All as Read

```
POST /api/notifications/mark-all-read
```

**Response `200 OK`:**

```json
{
  "success": true
}
```

**Flutter Usage (e.g., on notification screen open):**
```dart
await http.post(
  Uri.parse('$baseUrl/api/notifications/mark-all-read'),
  headers: {'Authorization': 'Bearer $token'},
);
```

---

### 3.5 Register Device Token (FCM)

```
POST /api/notifications/device-tokens
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

await http.post(
  Uri.parse('$baseUrl/api/notifications/device-tokens'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'token': fcmToken,
    'device': Platform.isAndroid ? 'android' : 'ios',
  }),
);

// Listen for token refresh
fcm.onTokenRefresh.listen((newToken) {
  // Re-register with the same endpoint
});
```

---

### 3.6 Delete Device Token

```
DELETE /api/notifications/device-tokens
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

await http.delete(
  Uri.parse('$baseUrl/api/notifications/device-tokens'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({'token': fcmToken}),
);
```

---

### 3.7 Send Test Notification (Debug)

```
POST /api/notifications/send-test
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

**Payload:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "type": "App\\Notifications\\GenericNotification",
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
      Uri.parse('$baseUrl/api/notifications/device-tokens'),
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
        Uri.parse('$baseUrl/api/notifications/device-tokens'),
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

All notification categories and the activities that trigger them:

| Category | Triggered By | Payload Fields |
|----------|-------------|----------------|
| `like` | Someone likes your post/reel | `title`, `message`, `actor_id`, `actor_name`, `actor_avatar`, `target_type`, `target_id` |
| `comment` | Someone comments on your post (or replies to your comment) | `title`, `message`, `actor_id`, `actor_name`, `actor_avatar`, `body`, `target_type`, `target_id` |
| `follow` | Someone follows you | `title`, `message`, `actor_id`, `actor_name`, `actor_avatar` |
| `new_order` | Customer places an order (sent to seller) | `title`, `message`, `order_id`, `order_uuid`, `amount`, `buyer_name`, `buyer_id` |
| `payment_success` | Payment completed successfully | Sent via `ProcessOrderPayment` job — includes order/payment details |
| `order_status` | Seller updates order status (sent to buyer) | Order and status details |
| `order_cancelled` | Order is cancelled | Cancellation details |
| `chat_message` | New chat message received | `chat_id`, `sender_name`, `content` |
| `admin_action` | Admin blocks/unblocks/verifies user | `title`, `message`, action details |
| `content_approved` | Admin approves your product/service/ad | `title`, `message`, `target_type`, `target_id` |
| `content_rejected` | Admin rejects your product/service/ad | `title`, `message`, `reason`, `target_type`, `target_id` |
| `post_flagged` | Admin flags/deletes your post | `title`, `message`, `post_id`, `status` |
| `enquiry_update` | Seller replies to your enquiry | `enquiry_id`, `status`, `message`, `reply` |
| `welcome` | User registration | `title`, `message` |
| `broadcast` | Admin broadcast to all users | `title`, `message` |
| `custom` | Admin sends direct message to user | `title`, `message` |

---

## 7. Unread Count Caching System

The system maintains a **cached unread count** in the `notification_unread_counts` table for fast badge display without scanning the full `notifications` table.

### Flow

1. **On notification sent**: The `UpdateUnreadNotificationCount` listener increments `unread_count` for the recipient user
2. **On mark-as-read**: The API decrements `unread_count` by 1
3. **On mark-all-as-read**: The API resets `unread_count` to 0
4. **On request**: The `unreadCount()` API returns the cached value

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
3. [ ] Get FCM token and call `POST /api/notifications/device-tokens`
4. [ ] Set up FCM token refresh listener: `firebaseMessaging.onTokenRefresh`
5. [ ] Set up foreground message handler: `FirebaseMessaging.onMessage`
6. [ ] Set up background message handler: `FirebaseMessaging.onMessageOpenedApp`
7. [ ] Connect to Reverb WebSocket via Laravel Echo
8. [ ] Subscribe to private channel: `echo.private('App.Models.User.$userId')`
9. [ ] Listen for `.notification` events to update badge in real-time
10. [ ] Fetch initial unread count: `GET /api/notifications/unread-count`

### On Logout

1. [ ] Call `DELETE /api/notifications/device-tokens` with current FCM token
2. [ ] Disconnect Reverb WebSocket connection
3. [ ] Clear local notification state

### Notification Screen

- [ ] Call `GET /api/notifications` with pagination (20 per page)
- [ ] Show `data[].payload.title` and `data[].payload.message`
- [ ] Show `data[].created_at` as relative time
- [ ] Show unread indicator when `data[].read_at` is null
- [ ] On tap: call `POST /api/notifications/mark-read` and navigate based on `category`
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
| `GET` | `/api/notifications` | ✅ Bearer | List notifications (paginated) |
| `GET` | `/api/notifications/unread-count` | ✅ Bearer | Get unread badge count |
| `POST` | `/api/notifications/mark-read` | ✅ Bearer | Mark single as read |
| `POST` | `/api/notifications/mark-all-read` | ✅ Bearer | Mark all as read |
| `POST` | `/api/notifications/send-test` | ✅ Bearer | Debug: send test notification |
| `POST` | `/api/notifications/device-tokens` | ✅ Bearer | Register FCM device token |
| `DELETE` | `/api/notifications/device-tokens` | ✅ Bearer | Remove FCM device token |
