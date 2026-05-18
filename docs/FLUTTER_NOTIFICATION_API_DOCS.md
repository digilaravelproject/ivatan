# Notification System API Documentation (Flutter)

**Version:** 1.0  
**Last Updated:** May 2026  
**Project:** Ivatan Social Platform

---

## 1. Overview

The notification system supports **in-app notifications** (database + broadcast) and **FCM push notifications** (Firebase Cloud Messaging). Notifications are delivered via:

| Channel | Description | Requires |
|---------|-------------|----------|
| `database` | Stored in DB, retrievable via API | Sanctum token |
| `broadcast` | Real-time via Laravel Echo (Reverb/Pusher) | Sanctum token + WebSocket |
| `fcm` | Push notification via Firebase Cloud Messaging | FCM device token registration |

### Notification Categories

| Category | Display Title | Push Enabled |
|----------|--------------|:------------:|
| `like` | New Like | Yes |
| `comment` | New Comment | Yes |
| `follow` | New Follower | Yes |
| `new_order` | New Order | Yes |
| `payment_success` | Payment Successful | Yes |
| `order_status` | Order Update | Yes |
| `order_cancelled` | Order Cancelled | Yes |
| `admin_action` | Account Update | Yes |
| `chat_message` | New Message | Yes |
| `custom` | Notification | No |
| `broadcast` | Announcement | Yes |
| `post_flagged` | Post Update | Yes |
| `welcome` | Welcome | Yes |

---

## 2. Authentication

All notification APIs require **Laravel Sanctum** authentication.

### Obtaining Token

```
POST /api/auth/login
Body: { "email": "...", "password": "..." }
Response: { "data": { "token": "sanctum-token-here" } }
```

### Using Token

Include in every request header:

```
Authorization: Bearer <sanctum-token>
Content-Type: application/json
Accept: application/json
```

---

## 3. Base URL

```
Production: https://www.ivatan.in/api/v1
Development: http://localhost:8000/api/v1
```

---

## 4. API Endpoints

### 4.1 List Notifications

Paginated list of the authenticated user's notifications.

**Endpoint:** `GET /notifications`

**Headers:**
```
Authorization: Bearer <token>
```

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `only` | string | `all` | Filter: `all` or `unread` |
| `per_page` | int | `20` | Items per page |

**Flutter Example:**
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> fetchNotifications({
  String? only,
  int perPage = 20,
}) async {
  final queryParams = <String, String>{
    'per_page': perPage.toString(),
  };
  if (only != null) queryParams['only'] = only;

  final uri = Uri.https(
    'www.ivatan.in', '/api/v1/notifications', queryParams,
  );

  final response = await http.get(
    uri,
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  return jsonDecode(response.body);
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": "9a8b7c6d-...",
                "type": "App\\Notifications\\GenericNotification",
                "data": {
                    "category": "like",
                    "payload": {
                        "title": "New Like",
                        "message": "John liked your post",
                        "action_url": null
                    },
                    "sent_at": "2026-05-18T10:30:00.000000Z"
                },
                "read_at": null,
                "created_at": "2026-05-18T10:30:00.000000Z"
            }
        ],
        "total": 42,
        "per_page": 20,
        "last_page": 3
    }
}
```

---

### 4.2 Get Unread Count

**Endpoint:** `GET /notifications/unread-count`

**Headers:**
```
Authorization: Bearer <token>
```

**Flutter Example:**
```dart
Future<int> getUnreadCount() async {
  final response = await http.get(
    Uri.https('www.ivatan.in', '/api/v1/notifications/unread-count'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  if (response.statusCode == 200) {
    final body = jsonDecode(response.body);
    return body['unread'] as int;
  }
  return 0;
}
```

**Response (200):**
```json
{
    "success": true,
    "unread": 5
}
```

---

### 4.3 Mark Notification as Read

**Endpoint:** `POST /notifications/mark-read`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
    "notification_id": "9a8b7c6d-... (UUID)"
}
```

**Flutter Example:**
```dart
Future<bool> markAsRead(String notificationId) async {
  final response = await http.post(
    Uri.https('www.ivatan.in', '/api/v1/notifications/mark-read'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'notification_id': notificationId,
    }),
  );

  return response.statusCode == 200;
}
```

**Response (200):**
```json
{
    "success": true
}
```

**Error Response (422) - Invalid UUID:**
```json
{
    "message": "The notification id must be a valid UUID.",
    "errors": {
        "notification_id": ["The notification id must be a valid UUID."]
    }
}
```

---

### 4.4 Mark All Notifications as Read

**Endpoint:** `POST /notifications/mark-all-read`

**Headers:**
```
Authorization: Bearer <token>
```

**Flutter Example:**
```dart
Future<bool> markAllAsRead() async {
  final response = await http.post(
    Uri.https('www.ivatan.in', '/api/v1/notifications/mark-all-read'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  return response.statusCode == 200;
}
```

**Response (200):**
```json
{
    "success": true
}
```

---

### 4.5 Register FCM Device Token

Register this device for push notifications. Call this after receiving an FCM token from Firebase.

**Endpoint:** `POST /notifications/device-tokens`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `token` | string | Yes | FCM device token |
| `device` | string | No | `ios`, `android`, or `web` |

**Flutter Example (using `firebase_messaging`):**
```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class NotificationService {
  final String baseUrl = 'https://www.ivatan.in';
  String? _authToken;

  void setAuthToken(String token) => _authToken = token;

  Future<void> registerFcmToken() async {
    final messaging = FirebaseMessaging.instance;

    // Request permission (iOS)
    await messaging.requestPermission();

    // Get FCM token
    final fcmToken = await messaging.getToken();
    if (fcmToken == null) return;

    // Send to backend
    final response = await http.post(
      Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
      headers: {
        'Authorization': 'Bearer $_authToken',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'token': fcmToken,
        'device': 'android', // or 'ios'
      }),
    );

    if (response.statusCode != 200) {
      debugPrint('Failed to register FCM token: ${response.body}');
    }
  }
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Device token registered."
}
```

---

### 4.6 Delete FCM Device Token

Call when the user logs out or the FCM token changes.

**Endpoint:** `DELETE /notifications/device-tokens`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
    "token": "fcm-device-token-here"
}
```

**Flutter Example:**
```dart
Future<void> deleteFcmToken(String fcmToken) async {
  final response = await http.delete(
    Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
    headers: {
      'Authorization': 'Bearer $_authToken',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'token': fcmToken,
    }),
  );
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Device token removed."
}
```

**Note:** On user logout, always delete the FCM token to stop push notifications.

---

### 4.7 Send Test Notification (Debug)

**Endpoint:** `POST /notifications/send-test`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": 1,
    "category": "custom",
    "payload": {
        "title": "Test Title",
        "message": "This is a test notification"
    }
}
```

**Response (200):**
```json
{
    "success": true
}
```

---

## 5. FCM Push Notification Handling in Flutter

### 5.1 Setup Dependencies

```yaml
dependencies:
  firebase_messaging: ^15.0.0
  firebase_core: ^3.0.0
  flutter_local_notifications: ^18.0.0
  http: ^1.2.0
```

### 5.2 Initialize Firebase

```dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  runApp(const MyApp());
}
```

### 5.3 Handle Incoming Messages

```dart
class PushNotificationService {
  final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();
  final FirebaseMessaging _messaging = FirebaseMessaging.instance;

  Future<void> initialize() async {
    // Request permissions
    final settings = await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );

    // Initialize local notifications for displaying when app is in foreground
    const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosSettings = DarwinInitializationSettings();
    await _localNotifications.initialize(
      const InitializationSettings(
        android: androidSettings,
        iOS: iosSettings,
      ),
      onDidReceiveNotificationResponse: _handleNotificationTap,
    );

    // Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

    // Handle background tap
    FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);

    // Handle terminated state tap
    final initialMessage = await _messaging.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationTap(initialMessage.data);
    }

    // Listen for token refresh
    _messaging.onTokenRefresh.listen(_onTokenRefresh);
  }

  void _handleForegroundMessage(RemoteMessage message) {
    final notification = message.notification;
    final data = message.data;

    if (notification != null) {
      _localNotifications.show(
        notification.hashCode,
        notification.title,
        notification.body,
        const NotificationDetails(
          android: AndroidNotificationDetails(
            'default_channel',
            'Default Channel',
            importance: Importance.high,
            priority: Priority.high,
          ),
          iOS: DarwinNotificationDetails(),
        ),
        payload: jsonEncode(data), // pass data for tap handling
      );
    }
  }

  void _handleNotificationTap(NotificationResponse? response) {
    if (response?.payload != null) {
      final data = jsonDecode(response!.payload!);
      _navigateToScreen(data);
    }
  }

  void _handleNotificationTap(Map<String, dynamic> data) {
    final category = data['category'];
    // Navigate based on category
    switch (category) {
      case 'like':
      case 'comment':
        // Navigate to post
        break;
      case 'follow':
        // Navigate to profile
        break;
      case 'chat_message':
        // Navigate to chat
        break;
      case 'new_order':
      case 'order_status':
      case 'order_cancelled':
        // Navigate to order details
        break;
      default:
        // Navigate to notification list
        break;
    }
  }

  Future<void> _onTokenRefresh(String newToken) async {
    // Re-register the new token with the backend
    await registerFcmToken(newToken);
  }
}
```

### 5.4 FCM Payload Structure

When a push notification arrives, the payload contains:

```json
{
  "notification": {
    "title": "New Like",
    "body": "John liked your post"
  },
  "data": {
    "category": "like",
    "payload": "{\"title\":\"New Like\",\"message\":\"John liked your post\"}",
    "sent_at": "2026-05-18T10:30:00.000000Z",
    "click_action": "FLUTTER_NOTIFICATION_CLICK"
  }
}
```

The `data.category` field determines which screen to navigate to on tap.

---

## 6. Real-time Notifications with Laravel Echo

### 6.1 Setup Echo in Flutter

```yaml
dependencies:
  laravel_echo: ^1.0.0
  pusher_client: ^2.0.0  # or reverb_client if using Reverb
```

### 6.2 Connect and Listen

```dart
import 'package:laravel_echo/laravel_echo.dart';
import 'package:pusher_client/pusher_client.dart';

class RealtimeNotificationService {
  late Echo echo;

  void connect(String authToken) {
    final options = PusherOptions(
      auth: Auth(
        headers: {
          'Authorization': 'Bearer $authToken',
        },
      ),
      host: 'www.ivatan.in',
      wsPort: 6001, // Reverb default
      wssPort: 6001,
      encrypted: true,
    );

    echo = Echo(
      options: PusherChannelOptions(
        'reverb',
        'your-reverb-app-key',
        options,
      ),
    );
  }

  void listenForNotifications(int userId) {
    // Listen on the user's private notification channel
    echo
        .private('App.Models.User.$userId')
        .notification((notification) {
      // Handle real-time notification
      print('New notification: ${notification.data.category}');
      // Update local state / show in-app badge
    });
  }

  void disconnect() {
    echo.disconnect();
  }
}
```

### 6.3 Channel Structure

| Channel | Purpose |
|---------|---------|
| `private-App.Models.User.{userId}` | User-specific notifications (Laravel model broadcasting) |

---

## 7. Full Flutter Integration Example

```dart
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class NotificationsProvider extends ChangeNotifier {
  final String baseUrl = 'https://www.ivatan.in';
  String? _authToken;

  List<NotificationItem> _notifications = [];
  int _unreadCount = 0;
  bool _loading = false;

  List<NotificationItem> get notifications => _notifications;
  int get unreadCount => _unreadCount;
  bool get loading => _loading;

  void setAuthToken(String token) => _authToken = token;

  // ─── FCM Lifecycle ─────────────────────────────────

  Future<void> initFcm() async {
    final messaging = FirebaseMessaging.instance;

    // Foreground handler
    FirebaseMessaging.onMessage.listen(_onForegroundMessage);
    // Background tap handler
    FirebaseMessaging.onMessageOpenedApp.listen(_onBackgroundTap);

    // Handle initial message (terminated state)
    final initialMsg = await messaging.getInitialMessage();
    if (initialMsg != null) _handleNotificationTap(initialMsg.data);

    // Request permission & get token
    await messaging.requestPermission();
    final token = await messaging.getToken();
    if (token != null && _authToken != null) {
      await _registerDeviceToken(token);
    }

    // Token refresh
    messaging.onTokenRefresh.listen((newToken) {
      _registerDeviceToken(newToken);
    });
  }

  Future<void> _registerDeviceToken(String fcmToken) async {
    try {
      await http.post(
        Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
        headers: _headers,
        body: jsonEncode({
          'token': fcmToken,
          'device': 'android',
        }),
      );
    } catch (e) {
      debugPrint('FCM token registration failed: $e');
    }
  }

  Future<void> removeDeviceToken(String fcmToken) async {
    try {
      await http.delete(
        Uri.parse('$baseUrl/api/v1/notifications/device-tokens'),
        headers: _headers,
        body: jsonEncode({'token': fcmToken}),
      );
    } catch (e) {
      debugPrint('FCM token removal failed: $e');
    }
  }

  void _onForegroundMessage(RemoteMessage message) {
    // Show local notification for foreground push
    // Or just update in-app notification count
    fetchUnreadCount();
  }

  void _onBackgroundTap(RemoteMessage message) {
    _handleNotificationTap(message.data);
  }

  void _handleNotificationTap(Map<String, dynamic> data) {
    // Navigate to relevant screen based on category
    final category = data['category'];
    // route based on category...
  }

  // ─── API Calls ─────────────────────────────────────

  Map<String, String> get _headers => {
    'Authorization': 'Bearer $_authToken',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  };

  Future<void> fetchNotifications({int page = 1}) async {
    _loading = true;
    notifyListeners();

    try {
      final uri = Uri.parse('$baseUrl/api/v1/notifications')
          .replace(queryParameters: {'page': '$page'});
      final response = await http.get(uri, headers: _headers);

      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        final List data = body['data']['data'];
        _notifications = data.map((n) => NotificationItem.fromJson(n)).toList();
      }
    } catch (e) {
      debugPrint('Fetch notifications failed: $e');
    }

    _loading = false;
    notifyListeners();
  }

  Future<void> fetchUnreadCount() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/api/v1/notifications/unread-count'),
        headers: _headers,
      );
      if (response.statusCode == 200) {
        _unreadCount = jsonDecode(response.body)['unread'] ?? 0;
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Fetch unread count failed: $e');
    }
  }

  Future<bool> markAsRead(String notificationId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/api/v1/notifications/mark-read'),
        headers: _headers,
        body: jsonEncode({'notification_id': notificationId}),
      );
      if (response.statusCode == 200) {
        await fetchUnreadCount();
        return true;
      }
    } catch (e) {
      debugPrint('Mark as read failed: $e');
    }
    return false;
  }

  Future<bool> markAllAsRead() async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/api/v1/notifications/mark-all-read'),
        headers: _headers,
      );
      if (response.statusCode == 200) {
        _unreadCount = 0;
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Mark all read failed: $e');
    }
    return false;
  }
}

// ─── Model ────────────────────────────────────────────

class NotificationItem {
  final String id;
  final String category;
  final String? title;
  final String? message;
  final DateTime? readAt;
  final DateTime createdAt;

  NotificationItem({
    required this.id,
    required this.category,
    this.title,
    this.message,
    this.readAt,
    required this.createdAt,
  });

  bool get isUnread => readAt == null;

  factory NotificationItem.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};
    final payload = data['payload'] as Map<String, dynamic>? ?? {};

    return NotificationItem(
      id: json['id'] as String,
      category: payload['category'] as String? ?? data['category'] as String? ?? 'unknown',
      title: payload['title'] as String?,
      message: payload['message'] as String?,
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
```

---

## 8. Notification UI (Flutter Widget)

```dart
import 'package:flutter/material.dart';

class NotificationBadge extends StatelessWidget {
  final int count;

  const NotificationBadge({super.key, required this.count});

  @override
  Widget build(BuildContext context) {
    if (count == 0) return const SizedBox.shrink();

    return Container(
      padding: const EdgeInsets.all(4),
      decoration: const BoxDecoration(
        color: Colors.red,
        shape: BoxShape.circle,
      ),
      child: Text(
        count > 99 ? '99+' : '$count',
        style: const TextStyle(
          color: Colors.white,
          fontSize: 10,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}

class NotificationListTile extends StatelessWidget {
  final NotificationItem notification;
  final VoidCallback? onTap;

  const NotificationListTile({
    super.key,
    required this.notification,
    this.onTap,
  });

  IconData get _icon {
    switch (notification.category) {
      case 'like': return Icons.favorite;
      case 'comment': return Icons.comment;
      case 'follow': return Icons.person_add;
      case 'chat_message': return Icons.chat;
      case 'new_order':
      case 'order_status':
      case 'order_cancelled': return Icons.shopping_bag;
      case 'admin_action':
      case 'broadcast': return Icons.campaign;
      default: return Icons.notifications;
    }
  }

  Color get _iconColor {
    return notification.isUnread ? Colors.blue : Colors.grey;
  }

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CircleAvatar(
        backgroundColor: _iconColor.withOpacity(0.1),
        child: Icon(_icon, color: _iconColor, size: 20),
      ),
      title: Text(
        notification.title ?? 'Notification',
        style: TextStyle(
          fontWeight: notification.isUnread ? FontWeight.bold : FontWeight.normal,
        ),
      ),
      subtitle: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            notification.message ?? '',
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 2),
          Text(
            _formatTime(notification.createdAt),
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: Colors.grey,
              fontSize: 11,
            ),
          ),
        ],
      ),
      trailing: notification.isUnread
          ? Container(
              width: 8,
              height: 8,
              decoration: const BoxDecoration(
                color: Colors.blue,
                shape: BoxShape.circle,
              ),
            )
          : null,
      onTap: onTap,
    );
  }

  String _formatTime(DateTime dt) {
    final now = DateTime.now();
    final diff = now.difference(dt);

    if (diff.inMinutes < 1) return 'Just now';
    if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
    if (diff.inHours < 24) return '${diff.inHours}h ago';
    if (diff.inDays < 7) return '${diff.inDays}d ago';
    return '${dt.month}/${dt.day}/${dt.year}';
  }
}
```

---

## 9. Complete Integration Checklist

### Authentication
- [ ] Obtain Sanctum token on login
- [ ] Store token securely (flutter_secure_storage)
- [ ] Pass token in all API headers
- [ ] Handle 401 responses (token expired)

### FCM Push
- [ ] Initialize Firebase
- [ ] Request notification permissions (Android 13+, iOS)
- [ ] Get FCM token on app start
- [ ] Register token via `POST /notifications/device-tokens`
- [ ] Listen for `onTokenRefresh` and re-register
- [ ] Handle foreground messages (show local notification)
- [ ] Handle background tap (navigate to screen)
- [ ] Handle terminated state tap (check `getInitialMessage`)
- [ ] Delete token on logout via `DELETE /notifications/device-tokens`

### In-App Notifications
- [ ] Fetch notifications on app start
- [ ] Implement pagination (scroll to load more)
- [ ] Display unread count badge
- [ ] Mark individual notification as read on tap
- [ ] "Mark all as read" functionality
- [ ] Filter by `only=unread` for unread tab
- [ ] Pull-to-refresh for notification list

### Real-time (Optional)
- [ ] Connect Laravel Echo on app start
- [ ] Listen on `private-App.Models.User.{id}`
- [ ] Update unread count on incoming notification
- [ ] Show in-app toast/banner for new notifications

### Edge Cases
- [ ] Network failure during API calls (retry / show error)
- [ ] Empty state when no notifications
- [ ] Loading state during fetch
- [ ] 99+ unread count display
- [ ] Very long notification messages (truncate)
- [ ] Special characters in titles/messages
- [ ] Handle rapid consecutive notifications

---

## 10. Error Handling

### API Error Codes

| Status Code | Meaning | Handling |
|-------------|---------|----------|
| 401 | Unauthenticated | Redirect to login |
| 403 | Forbidden | Show permission error |
| 404 | Not found | Show "not found" |
| 422 | Validation error | Show field errors |
| 429 | Rate limited | Retry after delay |
| 500 | Server error | Show generic error |

### HTTP Client Setup (with retry)

```dart
import 'package:http/http.dart' as http;

class ApiClient {
  final http.Client _client = http.Client();
  String? _token;

  void setToken(String? token) => _token = token;

  Map<String, String> get _headers => {
    'Authorization': 'Bearer $_token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  };

  Future<http.Response> get(String path, {Map<String, String>? params}) async {
    final uri = Uri.parse('https://www.ivatan.in$path')
        .replace(queryParameters: params);
    return _client.get(uri, headers: _headers);
  }

  Future<http.Response> post(String path, {Map<String, dynamic>? body}) async {
    return _client.post(
      Uri.parse('https://www.ivatan.in$path'),
      headers: _headers,
      body: body != null ? jsonEncode(body) : null,
    );
  }

  Future<http.Response> delete(String path, {Map<String, dynamic>? body}) async {
    final request = http.Request('DELETE', Uri.parse('https://www.ivatan.in$path'));
    request.headers.addAll(_headers);
    if (body != null) request.body = jsonEncode(body);
    return _client.send(request).then((r) => http.Response.fromStream(r));
  }

  void dispose() => _client.close();
}
```

---

## 11. Notification Lifecycle (App State Handling)

| App State | How Notification Appears | Action |
|-----------|------------------------|--------|
| **Foreground** | Custom in-app UI (banner/toast) | Update notification list + badge |
| **Background** | System notification tray | FCM handles display |
| **Terminated** | System notification tray | `getInitialMessage()` on cold start |

---

## 12. Admin Notification Panel (Web Only, Reference)

The admin panel at `/admin/notifications` provides:
- List/search/filter all user notifications
- View notification details with full payload
- Send notification to a single user
- Send broadcast to all users

---

## 13. Notification Data Flow Summary

```
Firebase FCM Server
    ↑ (push)
Flutter App ──registerToken()──> Laravel Backend
    ↓ (realtime)
Laravel Echo (WebSocket)
    ↓
Flutter App (real-time update)
    ↓ (poll)
GET /notifications (API)
    ↓
Notification List UI
```

---

*End of Documentation*
