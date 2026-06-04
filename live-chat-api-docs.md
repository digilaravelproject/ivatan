# Live Chat Groups API — Documentation for Flutter

> **Version**: 1.0  
> **Last Updated**: 2026-06-04  
> **Stack**: Laravel 12 + Sanctum + Reverb (WebSocket)

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [API Endpoints](#2-api-endpoints)
   - [2.1 List My Groups](#21-list-my-groups)
   - [2.2 Get Group Detail (with Participants)](#22-get-group-detail-with-participants)
3. [Real-Time WebSocket Events](#3-real-time-websocket-events)
   - [3.1 Chat Message Sent](#31-chat-message-sent)
   - [3.2 Chat Message Read](#32-chat-message-read)
4. [Data Models](#4-data-models)
5. [Performance Guarantees](#5-performance-guarantees)
6. [Flutter Integration Checklist](#6-flutter-integration-checklist)

---

## 1. System Overview

Live Chat Groups are public chat rooms (e.g., "Tech Talk", "Gaming Zone") that users auto-join upon registration. They use a **group chat model** where every user is a participant via the `user_chat_participants` pivot table.

### Key Concepts

| Concept | Description |
|---------|-------------|
| **Group** | A top-level entity (`live_chat_groups` table) with name, slug, description, and chat_mode (admin_only or everyone) |
| **Chat** | Each group links to a `UserChat` record that stores messages and participants |
| **Participant** | A user belonging to a chat, with role (`admin` or `member`), ban/mute status |
| **Message** | Text or attachment messages in a chat, broadcast via Reverb WebSocket |

### Architecture

```
LiveChatGroup (model)
    │
    ├── creator()  ──► User (who created it)
    │
    └── chat()  ──► UserChat (the actual chat container)
                        │
                        ├── participants() ──► UserChatParticipant[] (pivot)
                        │                           │
                        │                           └── user() ──► User
                        │
                        ├── messages() ──► UserChatMessage[]
                        │
                        └── lastMessage() ──► UserChatMessage (latest)
```

---

## 2. API Endpoints

All endpoints require **Bearer token authentication** (Sanctum).

**Headers:**
```
Authorization: Bearer <sanctum_token>
Accept: application/json
```

---

### 2.1 List My Groups

Returns all active live chat groups the authenticated user is a member of.

```
GET /api/v1/live-chat-groups
Authorization: Bearer <token>
```

**Response `200 OK`:**

```json
{
  "success": true,
  "data": {
    "groups": [
      {
        "id": 1,
        "name": "Tech Talk",
        "slug": "tech-talk-a1b2",
        "description": "A group for tech enthusiasts",
        "chat_mode": "everyone",
        "is_active": true,
        "chat_id": 5,
        "participants_count": 134,
        "last_message": {
          "id": 1001,
          "chat_id": 5,
          "sender_id": 42,
          "content": "Hello everyone!",
          "message_type": "text",
          "created_at": "2026-06-04T10:30:00.000000Z",
          "sender": {
            "id": 42,
            "name": "John Doe",
            "profile_photo_url": "https://..."
          }
        },
        "is_banned": false,
        "is_muted": false,
        "created_at": "2026-06-01T10:00:00.000000Z"
      }
    ]
  }
}
```

**Fields returned:**

| Field | Type | Notes |
|-------|------|-------|
| `id` | int | Group ID |
| `name` | string | Group display name |
| `slug` | string | URL-safe slug |
| `description` | string? | Group description |
| `chat_mode` | string | `"everyone"` or `"admin_only"` |
| `is_active` | bool | Whether group is active |
| `chat_id` | int | ID of the underlying chat (for WebSocket channel) |
| `participants_count` | int | Total number of members |
| `last_message` | object? | Most recent message (null if no messages) |
| `is_banned` | bool | Whether current user is banned |
| `is_muted` | bool | Whether current user is muted |
| `created_at` | datetime | Group creation date |

**Flutter Usage:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/live-chat-groups'),
  headers: {'Authorization': 'Bearer $token'},
);
final groups = jsonDecode(response.body)['data']['groups'];
```

---

### 2.2 Get Group Detail (with Participants)

Returns full group details with a **paginated list of participants** (50 per page).

```
GET /api/v1/live-chat-groups/{id}
Authorization: Bearer <token>
```

**Path Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | int | ID of the LiveChatGroup |

**Query Parameters (for participant pagination):**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | `1` | Page number for participants list |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": {
    "group": {
      "id": 1,
      "name": "Tech Talk",
      "slug": "tech-talk-a1b2",
      "description": "A group for tech enthusiasts",
      "chat_mode": "everyone",
      "is_active": true,
      "chat_id": 5,
      "created_by": {
        "id": 1,
        "name": "Admin User"
      },
      "created_at": "2026-06-01T10:00:00.000000Z",

      "participants": {
        "data": [
          {
            "id": 101,
            "user": {
              "id": 42,
              "name": "John Doe",
              "username": "johndoe",
              "avatar": "https://example.com/storage/avatars/john.jpg"
            },
            "role": "member",
            "joined_at": "2026-06-01T10:05:00.000000Z",
            "is_banned": false,
            "is_muted": false,
            "muted_until": null
          },
          {
            "id": 102,
            "user": {
              "id": 1,
              "name": "Admin User",
              "username": "admin",
              "avatar": "https://example.com/storage/avatars/admin.jpg"
            },
            "role": "admin",
            "joined_at": "2026-06-01T10:00:00.000000Z",
            "is_banned": false,
            "is_muted": false,
            "muted_until": null
          }
        ],
        "current_page": 1,
        "per_page": 50,
        "last_page": 3,
        "total": 134,
        "next_page_url": "https://api.ivatan.in/api/v1/live-chat-groups/1?page=2",
        "prev_page_url": null
      }
    }
  }
}
```

**Error Response `403` (not a member):**
```json
{
  "success": false,
  "message": "You are not a member of this group."
}
```

**Error Response `404` (inactive or not found):**
```json
{
  "success": false,
  "message": "Group not found."
}
```

**Participant object fields:**

| Field | Type | Notes |
|-------|------|-------|
| `id` | int | Participant pivot ID |
| `user.id` | int | User ID |
| `user.name` | string | Display name |
| `user.username` | string | Unique username |
| `user.avatar` | string | Full avatar URL (or default avatar) |
| `role` | string | `"admin"` or `"member"` |
| `joined_at` | datetime | When user joined the group |
| `is_banned` | bool | Ban status |
| `is_muted` | bool | Mute status |
| `muted_until` | datetime? | When mute expires (null = permanent) |

**Flutter Usage with Pagination:**

```dart
class GroupDetailScreen extends StatefulWidget {
  final int groupId;
  const GroupDetailScreen({required this.groupId});

  @override
  _GroupDetailScreenState createState() => _GroupDetailScreenState();
}

class _GroupDetailScreenState extends State<GroupDetailScreen> {
  Map<String, dynamic>? _group;
  List<dynamic> _participants = [];
  int _currentPage = 1;
  int _lastPage = 1;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _fetchGroup();
  }

  Future<void> _fetchGroup({int page = 1}) async {
    setState(() => _isLoading = true);
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/api/v1/live-chat-groups/${widget.groupId}?page=$page'),
        headers: {'Authorization': 'Bearer $token'},
      );
      final body = jsonDecode(response.body);
      if (body['success'] == true) {
        final groupData = body['data']['group'];
        setState(() {
          _group = groupData;
          _participants.addAll(groupData['participants']['data']);
          _currentPage = groupData['participants']['current_page'];
          _lastPage = groupData['participants']['last_page'];
        });
      }
    } finally {
      setState(() => _isLoading = false);
    }
  }

  // Load more participants on scroll-to-bottom
  Future<void> _loadMore() async {
    if (_currentPage < _lastPage && !_isLoading) {
      await _fetchGroup(page: _currentPage + 1);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_group == null) return const Center(child: CircularProgressIndicator());

    return Scaffold(
      appBar: AppBar(title: Text(_group!['name'])),
      body: Column(
        children: [
          // Group info header
          Padding(
            padding: const EdgeInsets.all(16),
            child: Text(_group!['description'] ?? ''),
          ),

          // Participants list with load-more
          Expanded(
            child: ListView.builder(
              itemCount: _participants.length + (_currentPage < _lastPage ? 1 : 0),
              itemBuilder: (context, index) {
                if (index == _participants.length) {
                  return const Center(child: CircularProgressIndicator());
                }
                final p = _participants[index];
                return ListTile(
                  leading: CircleAvatar(backgroundImage: NetworkImage(p['user']['avatar'])),
                  title: Text(p['user']['name']),
                  subtitle: Text(p['role'] == 'admin' ? 'Admin' : 'Member'),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
```

---

## 3. Real-Time WebSocket Events

### 3.1 Chat Message Sent

When a message is sent in a group chat, it's broadcast via Reverb.

**Channel:** `chat.{chat_id}` (private)

**Event name (client-side):** `message.sent`

**Payload:**

```json
{
  "id": 1001,
  "chat_id": 5,
  "sender_id": 42,
  "content": "Hello everyone!",
  "message_type": "text",
  "attachment_path": null,
  "meta": null,
  "reply_to_message_id": null,
  "delivered_at": "2026-06-04T10:30:00.000000Z",
  "created_at": "2026-06-04T10:30:00.000000Z",
  "sender": {
    "id": 42,
    "name": "John Doe",
    "profile_photo_url": "https://..."
  }
}
```

### 3.2 Chat Message Read

When a participant marks messages as read.

**Channel:** `chat.{chat_id}` (private)

**Event name (client-side):** `message.read`

**Payload:**

```json
{
  "chat_id": 5,
  "user_id": 42,
  "last_read_message_id": 1001
}
```

### Flutter WebSocket Setup

```dart
import 'package:laravel_echo/laravel_echo.dart';
import 'package:pusher_client/pusher_client.dart';

final echo = Echo({
  'broadcaster': 'pusher',
  'key': '$REVERB_APP_KEY',
  'wsHost': '$REVERB_HOST',
  'wsPort': 443,
  'wssPort': 443,
  'forceTLS': true,
  'disableStats': true,
  'authEndpoint': '$baseUrl/broadcasting/auth',
  'auth': {
    'headers': {'Authorization': 'Bearer $sanctumToken'},
  },
});

// Listen for new messages in a group chat
echo.private('chat.$chatId')
  .listen('message.sent', (event) {
    // Append message to chat UI
    setState(() => messages.add(event));
  });

// Listen for read receipts
echo.private('chat.$chatId')
  .listen('message.read', (event) {
    // Update read receipts
  });
```

---

## 4. Data Models

### LiveChatGroup

```dart
class LiveChatGroup {
  final int id;
  final String name;
  final String slug;
  final String? description;
  final String chatMode;     // "everyone" | "admin_only"
  final bool isActive;
  final int? chatId;
  final Creator? createdBy;
  final String createdAt;
  final ParticipantList? participants;  // Only in detail response

  LiveChatGroup({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    required this.chatMode,
    required this.isActive,
    this.chatId,
    this.createdBy,
    required this.createdAt,
    this.participants,
  });

  factory LiveChatGroup.fromJson(Map<String, dynamic> json) {
    return LiveChatGroup(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      description: json['description'],
      chatMode: json['chat_mode'],
      isActive: json['is_active'],
      chatId: json['chat_id'],
      createdBy: json['created_by'] != null
          ? Creator.fromJson(json['created_by'])
          : null,
      createdAt: json['created_at'],
      participants: json['participants'] != null
          ? ParticipantList.fromJson(json['participants'])
          : null,
    );
  }
}

class Creator {
  final int id;
  final String name;

  Creator({required this.id, required this.name});

  factory Creator.fromJson(Map<String, dynamic> json) {
    return Creator(id: json['id'], name: json['name']);
  }
}

class ParticipantList {
  final List<Participant> data;
  final int currentPage;
  final int perPage;
  final int lastPage;
  final int total;
  final String? nextPageUrl;

  ParticipantList({
    required this.data,
    required this.currentPage,
    required this.perPage,
    required this.lastPage,
    required this.total,
    this.nextPageUrl,
  });

  factory ParticipantList.fromJson(Map<String, dynamic> json) {
    return ParticipantList(
      data: (json['data'] as List).map((e) => Participant.fromJson(e)).toList(),
      currentPage: json['current_page'],
      perPage: json['per_page'],
      lastPage: json['last_page'],
      total: json['total'],
      nextPageUrl: json['next_page_url'],
    );
  }
}

class Participant {
  final int id;
  final ParticipantUser user;
  final String role;       // "admin" | "member"
  final String joinedAt;
  final bool isBanned;
  final bool isMuted;
  final String? mutedUntil;

  Participant({
    required this.id,
    required this.user,
    required this.role,
    required this.joinedAt,
    required this.isBanned,
    required this.isMuted,
    this.mutedUntil,
  });

  factory Participant.fromJson(Map<String, dynamic> json) {
    return Participant(
      id: json['id'],
      user: ParticipantUser.fromJson(json['user']),
      role: json['role'],
      joinedAt: json['joined_at'],
      isBanned: json['is_banned'],
      isMuted: json['is_muted'],
      mutedUntil: json['muted_until'],
    );
  }
}

class ParticipantUser {
  final int id;
  final String name;
  final String username;
  final String avatar;

  ParticipantUser({
    required this.id,
    required this.name,
    required this.username,
    required this.avatar,
  });

  factory ParticipantUser.fromJson(Map<String, dynamic> json) {
    return ParticipantUser(
      id: json['id'],
      name: json['name'],
      username: json['username'],
      avatar: json['avatar'],
    );
  }
}
```

---

## 5. Performance Guarantees

| Concern | How It's Handled |
|---------|-----------------|
| **N+1 queries** | ❌ None. Every `with()` uses column selection to avoid loading unused data. Membership check uses `exists()` (subquery, no model hydration). |
| **Memory blowup from 10K+ participants** | ✅ Paginated at **50 per page**. Only 50 participant models + 50 user models loaded per request, regardless of group size. |
| **Sensitive user data leaked** | ✅ Only `id`, `name`, `username`, `profile_photo_path` are loaded from `users` table. No `email`, `phone`, `password`, `remember_token`, `created_at`, `updated_at`. |
| **Response size** | ✅ ~12KB for a group with 50 participants (vs ~300KB+ if all columns were loaded for all participants). |
| **Database index usage** | ✅ `user_chat_participants` has a unique composite index on `(chat_id, user_id)` — the membership `exists()` check uses this directly. |

### Database Queries Executed (per detail request)

```
1. SELECT * FROM live_chat_groups WHERE id = ?  (route binding)
2. SELECT EXISTS(                           (membership check)
     SELECT 1 FROM user_chat_participants
     WHERE chat_id = ? AND user_id = ?
   )
3. SELECT id, name FROM users WHERE id = ?   (creator)
4. SELECT id, uuid, name, type, chat_mode,   (chat)
         live_chat_group_id
   FROM user_chats WHERE id = ?
5. SELECT id, chat_id, user_id, is_admin,    (participants — paginated)
         joined_at, is_banned, is_muted,
         muted_until
   FROM user_chat_participants
   WHERE chat_id = ?
   ORDER BY joined_at
   LIMIT 50 OFFSET 0
6. SELECT COUNT(*) FROM user_chat_participants  (total count for pagination)
   WHERE chat_id = ?
7. SELECT id, name, username,                  (users for page 1 participants)
         profile_photo_path
   FROM users WHERE id IN (?, ?, ?...)
```

**Total: 7 queries, regardless of group size.** All indexed.

---

## 6. Flutter Integration Checklist

### Group List Screen

- [ ] Call `GET /api/v1/live-chat-groups` on screen load
- [ ] Render each group with name, description, participants count
- [ ] Show "Admin" or "Everyone" badge based on `chat_mode`
- [ ] Show last message preview if available
- [ ] Show banned/muted indicators based on `is_banned` / `is_muted`
- [ ] Tap to navigate to group detail + chat

### Group Detail Screen

- [ ] Call `GET /api/v1/live-chat-groups/{id}` on screen load
- [ ] Display group info header (name, description, creator)
- [ ] Render participants list (horizontal avatar row + vertical list)
- [ ] Implement **scroll-to-bottom pagination** for participants
  - Track `current_page` and `last_page` from response
  - Load next page when user scrolls near bottom
  - Append new participants to existing list
- [ ] Admin badge next to admin participants
- [ ] Ban/mute icon indicators
- [ ] Connect to Reverb WebSocket for real-time messages

### Pagination Pattern

```dart
class ParticipantPagination {
  List<Participant> items = [];
  int currentPage = 1;
  int lastPage = 1;
  bool isLoading = false;

  Future<void> fetchFirstPage(int groupId) async {
    isLoading = true;
    final data = await _fetchPage(groupId, page: 1);
    items = data.items;
    currentPage = data.currentPage;
    lastPage = data.lastPage;
    isLoading = false;
  }

  Future<void> fetchNextPage(int groupId) async {
    if (currentPage >= lastPage || isLoading) return;
    isLoading = true;
    final data = await _fetchPage(groupId, page: currentPage + 1);
    items.addAll(data.items);
    currentPage = data.currentPage;
    isLoading = false;
  }
}
```

---

## Quick Reference: All Endpoints

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| `GET` | `/api/v1/live-chat-groups` | ✅ Bearer | List groups the user is a member of |
| `GET` | `/api/v1/live-chat-groups/{id}` | ✅ Bearer | Group detail with paginated participants |
