# Live Chat Group API Documentation

**Version:** 1.0  
**Last Updated:** May 2026  
**Project:** Ivatan Social Platform

---

## 1. Overview

This document covers the Live Chat Group feature APIs implemented in the Ivatan project. The system supports:

- **Group-based chat** with real-time messaging via Laravel Reverb
- **Two chat modes:**
  - `admin_only` - Only admins can send messages; regular users can only read
  - `everyone` - All participants can send messages
- **Moderation features** - Admin can ban/mute users from the group
- **Real-time events** - Message sent, message read notifications
- **Auto-join on registration** - New users are automatically added to active live chat groups

---

## 2. Authentication Requirements

All chat APIs (except public group listing) require authentication via **Laravel Sanctum**.

### How to Authenticate

1. **Login** via `POST /api/auth/login` to get a Sanctum token
2. **Include token** in all subsequent requests via header:

```
Authorization: Bearer <your_sanctum_token>
```

Or use the cookie-based authentication (if using web guard).

---

## 3. Base API URL

```
Production: https://www.ivatan.in/api/v1
Development: http://localhost:8000/api/v1
```

---

## 4. User Chat APIs

### 4.1 Fetch Live Chat Groups

Get the list of live chat groups that the authenticated user is a member of.

**Endpoint:** `GET /api/v1/live-chat-groups`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Response (200):**
```json
{
    "status": true,
    "message": "Success",
    "data": {
        "groups": [
            {
                "id": 1,
                "name": "General Discussion",
                "slug": "general-discussion-a1b2",
                "description": "General chat for all users",
                "chat_mode": "everyone",
                "is_active": true,
                "chat_id": 15,
                "participants_count": 142,
                "last_message": {
                    "id": 456,
                    "content": "Hello everyone!",
                    "message_type": "text",
                    "sender_id": 5,
                    "created_at": "2026-05-18T10:30:00Z"
                },
                "is_banned": false,
                "is_muted": false,
                "created_at": "2026-05-01T00:00:00Z"
            }
        ]
    }
}
```

**Pagination:** Uses Laravel's paginator - check `links` or `meta` in response for pagination data.

---

### 4.2 Fetch Single Live Chat Group

Get details of a specific live chat group.

**Endpoint:** `GET /api/v1/live-chat-groups/{liveChatGroup}`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Response (200):**
```json
{
    "status": true,
    "message": "Success",
    "data": {
        "group": {
            "id": 1,
            "name": "General Discussion",
            "slug": "general-discussion-a1b2",
            "description": "General chat for all users",
            "chat_mode": "everyone",
            "is_active": true,
            "chat_id": 15,
            "created_by": "Admin User",
            "created_at": "2026-05-01T00:00:00Z"
        }
    }
}
```

**Error Response (403):**
```json
{
    "status": false,
    "message": "You are not a member of this group.",
    "errors": []
}
```

---

### 4.3 Fetch Chat Inbox (All Chats)

Get list of all chats the user participates in, including live chat groups.

**Endpoint:** `GET /api/v1/chats`

**Query Parameters:**
- `filter` (optional): `groups`, `unread`, `read`, `business`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Response (200):**
```json
{
    "status": true,
    "message": "Success",
    "data": {
        "chats": {
            "data": [
                {
                    "id": 15,
                    "uuid": "abc-123-def",
                    "type": "group",
                    "name": "General Discussion",
                    "live_chat_group_id": 1,
                    "chat_mode": "everyone",
                    "last_message_at": "2026-05-18T10:30:00Z",
                    "participants_count": 142,
                    "participants": [
                        {
                            "id": 1,
                            "user_id": 5,
                            "is_admin": true,
                            "user": { ... }
                        }
                    ],
                    "last_message": { ... }
                }
            ],
            "links": { ... },
            "meta": { ... }
        }
    }
}
```

---

### 4.4 Fetch Messages in a Chat

Get messages for a specific chat (including live chat groups).

**Endpoint:** `GET /api/v1/chats/{chat}/messages`

**Query Parameters:**
- `after_id` (optional): For polling - get messages after this ID

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Response (200):**
```json
{
    "status": true,
    "message": "Success",
    "data": [
        {
            "id": 456,
            "uuid": "msg-uuid-123",
            "chat_id": 15,
            "sender_id": 5,
            "content": "Hello everyone!",
            "message_type": "text",
            "attachment_path": null,
            "reply_to_message_id": null,
            "created_at": "2026-05-18T10:30:00Z",
            "sender": {
                "id": 5,
                "name": "John Doe",
                "profile_photo_url": "https://..."
            },
            "reply_to": null
        }
    ]
}
```

**Pagination:** Uses cursor pagination - check `next_cursor`, `per_page` in response.

---

### 4.5 Send Message

Send a message to a chat (including live chat groups).

**Endpoint:** `POST /api/v1/chats/{chat}/messages`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Payload:**
```json
{
    "content": "Hello world!",
    "message_type": "text"
}
```

**With Attachment:**
```json
{
    "message_type": "image",
    "attachment": <file>
}
```

**Validation:**
- Either `content` or `attachment` is required
- `message_type`: `text`, `image`, or `file`
- `attachment`: max 20MB, mimes: jpeg, png, jpg, pdf, docx, zip

**Response (201):**
```json
{
    "status": true,
    "message": "Sent",
    "data": {
        "id": 457,
        "uuid": "msg-uuid-456",
        "chat_id": 15,
        "sender_id": 5,
        "content": "Hello world!",
        "message_type": "text",
        "attachment_path": null,
        "created_at": "2026-05-18T10:35:00Z",
        "sender": {
            "id": 5,
            "name": "John Doe",
            "profile_photo_url": "https://..."
        }
    }
}
```

**Error Responses:**

*User is banned (403):*
```json
{
    "status": false,
    "message": "You have been banned from this group.",
    "errors": []
}
```

*User is muted (403):*
```json
{
    "status": false,
    "message": "You are muted until 2026-05-18 11:35:00.",
    "errors": []
}
```

*Admin-only mode, user is not admin (403):*
```json
{
    "status": false,
    "message": "Only admins can send messages in this group.",
    "errors": []
}
```

---

### 4.6 Mark Messages as Read

Mark messages as read in a chat.

**Endpoint:** `POST /api/v1/chats/{chat}/read`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Payload:**
```json
{
    "last_read_message_id": 456
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Messages marked as read.",
    "data": []
}
```

---

### 4.7 Delete Message

Delete a message (for me or for everyone).

**Endpoint:** `DELETE /api/v1/chats/messages/{message}`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Payload:**
```json
{
    "delete_for_everyone": true
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Message deleted",
    "data": []
}
```

---

## 5. Admin Chat APIs (Web Panel)

The admin panel is accessible at `/admin` and includes full CRUD for live chat groups.

### 5.1 Admin Routes

All admin routes are under `/admin/live-chat-groups` with authentication middleware.

| Method | Endpoint | Action |
|--------|----------|--------|
| GET | `/admin/live-chat-groups` | List all groups |
| GET | `/admin/live-chat-groups/create` | Show create form |
| POST | `/admin/live-chat-groups` | Create new group |
| GET | `/admin/live-chat-groups/{group}` | View group details & participants |
| GET | `/admin/live-chat-groups/{group}/edit` | Show edit form |
| PUT | `/admin/live-chat-groups/{group}` | Update group |
| DELETE | `/admin/live-chat-groups/{group}` | Delete group |
| POST | `/admin/live-chat-groups/{group}/remove-participant` | Remove user |
| POST | `/admin/live-chat-groups/{group}/ban-participant` | Ban user |
| POST | `/admin/live-chat-groups/{group}/unban-participant` | Unban user |
| POST | `/admin/live-chat-groups/{group}/mute-participant` | Mute user |

### 5.2 Admin Create Group

**Form Data:**
```json
{
    "name": "New Group Name",
    "description": "Group description",
    "chat_mode": "everyone",  // or "admin_only"
    "is_active": true
}
```

### 5.3 Admin Moderation Actions

**Remove User:**
```json
{
    "user_id": 123
}
```

**Ban User:**
```json
{
    "user_id": 123
}
```

**Unban User:**
```json
{
    "user_id": 123
}
```

**Mute User (1 hour):**
```json
{
    "user_id": 123,
    "minutes": 60
}
```

---

## 6. Realtime Events

The system uses **Laravel Reverb** (or Pusher fallback) for real-time broadcasting.

### 6.1 Channel Names

All chat events are broadcast on **Private Channels**:

```
Private Channel: chat.{chatId}
```

Example: `chat.15` for chat ID 15

### 6.2 Authorization

Channel authorization is handled in `routes/channels.php`:

```php
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return UserChatParticipant::where('chat_id', $chatId)
        ->where('user_id', $user->id)
        ->exists();
});
```

Banned users are removed from participants, so they automatically lose channel access.

### 6.3 Event: MessageSent

Fired when a new message is sent (including system messages).

**Event Name:** `message.sent`

**Broadcast As:** `message.sent` on channel `chat.{chatId}`

**Payload:**
```json
{
    "id": 457,
    "chat_id": 15,
    "content": "Hello world!",
    "message_type": "text",
    "attachment_url": null,
    "is_mine": false,
    "status": "sent",
    "created_at": "2026-05-18T10:35:00Z",
    "sender": {
        "id": 5,
        "name": "John Doe",
        "avatar": "https://..."
    },
    "reply_to_id": null
}
```

### 6.4 Event: MessageRead

Fired when a user marks messages as read.

**Event Name:** `message.read`

**Broadcast As:** `message.read` on channel `chat.{chatId}`

**Payload:**
```json
{
    "user_id": 5,
    "last_read_message_id": 456,
    "read_at": "2026-05-18T10:36:00Z"
}
```

---

## 7. Laravel Echo Frontend Integration

### 7.1 Configuration

In your JavaScript/TypeScript setup:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Using Reverb (recommended)
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 443,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Fallback to Pusher
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

### 7.2 Listening to Events

**Listen for new messages:**
```javascript
const chatId = 15;

Echo.private(`chat.${chatId}`)
    .listen('message.sent', (event) => {
        console.log('New message:', event);
        // Update UI with new message
    })
    .listen('.message.sent', (event) => {
        // Alternative syntax
    });
```

**Listen for read receipts:**
```javascript
Echo.private(`chat.${chatId}`)
    .listen('message.read', (event) => {
        console.log('Messages read by:', event.user_id);
        console.log('Last read ID:', event.last_read_message_id);
        // Update UI to show messages as read
    });
```

### 7.3 Leaving Channels

```javascript
// When user leaves the chat
Echo.leave(`chat.${chatId}`);
```

### 7.4 Presence Channel (Future Enhancement)

Not currently implemented. Can be added later if admin presence indicators are needed.

---

## 8. Common API Response Format

### Success Response

```json
{
    "status": true,
    "message": "Success",
    "data": { ... }
}
```

### Error Response

```json
{
    "status": false,
    "message": "Error message here",
    "errors": []
}
```

### Paginated Response

```json
{
    "status": true,
    "message": "Success",
    "data": {
        "data": [ ... ],
        "links": {
            "next": "http://.../?cursor=abc",
            "prev": null
        },
        "meta": {
            "current_page": 1,
            "per_page": 30,
            "total": 150
        }
    }
}
```

---

## 9. Chat Mode Behavior

### admin_only Mode

- **Admins:** Can send messages, view all content
- **Regular Users:** Can only read messages, cannot send
- **API Behavior:** Returns `403` with message "Only admins can send messages in this group."

### everyone Mode

- **All Users:** Can send messages, view all content

### Moderation States

- **Banned User:** Removed from participant list, cannot join, cannot listen to channel
- **Muted User:** Stays in participant list, can listen (read), cannot send

---

## 10. Frontend Integration Flow

### 10.1 Initial Load

1. Authenticate user (obtain Sanctum token)
2. Fetch user's chats: `GET /api/v1/chats`
3. Filter for live groups: `GET /api/v1/chats?filter=groups`
4. Or fetch live groups directly: `GET /api/v1/live-chat-groups`

### 10.2 Enter Chat

1. Load messages: `GET /api/v1/chats/{chatId}/messages`
2. Subscribe to realtime channel: `Echo.private('chat.' + chatId)`
3. Listen for `message.sent` and `message.read` events

### 10.3 Send Message

1. POST to `POST /api/v1/chats/{chatId}/messages`
2. On success, message appears via API response
3. Real-time event also fires to other participants

### 10.4 Mark as Read

1. When user opens chat or scrolls to message
2. POST to `POST /api/v1/chats/{chatId}/read` with `last_read_message_id`
3. `message.read` event broadcast to channel

### 10.5 Handle Errors

- **Banned:** Show "You have been banned from this group" - redirect or show message
- **Muted:** Show mute duration message if applicable
- **Admin-only:** Show "Only admins can send messages" - disable send input

---

## 11. Testing Checklist for Frontend Developer

### Authentication
- [ ] Test login and token storage
- [ ] Test token expiration handling
- [ ] Test unauthenticated access (should return 401)

### Fetch Groups
- [ ] Test `GET /api/v1/live-chat-groups` with valid token
- [ ] Test with no groups (empty array response)
- [ ] Test with filter=groups

### Messages
- [ ] Test loading messages with pagination
- [ ] Test polling with `after_id` parameter
- [ ] Test sending text message
- [ ] Test sending image attachment
- [ ] Test sending file attachment
- [ ] Test validation errors (empty content, invalid type)

### Real-time
- [ ] Test receiving `message.sent` event
- [ ] Test receiving `message.read` event
- [ ] Test channel leave on chat exit
- [ ] Test reconnection on network issues

### Moderation States
- [ ] Test sending as banned user (403)
- [ ] Test sending as muted user (403)
- [ ] Test admin_only mode as regular user (403)
- [ ] Test admin_only mode as admin (should work)

### Edge Cases
- [ ] Test very long messages
- [ ] Test special characters in messages
- [ ] Test network failure during message send
- [ ] Test concurrent message sends
- [ ] Test deleted message display

---

## 12. Notes

- **Pagination:** Uses cursor-based pagination for messages (efficient for large datasets)
- **Attachments:** Stored in `storage/app/chat_attachments/{chat_id}/`
- **System Messages:** Messages with `sender_id: null` and `message_type: system` are auto-generated (e.g., "User joined", "User banned")
- **Media:** Images use Spatie Media Library - ensure proper disk configuration
- **Pusher Fallback:** If Reverb is not available, the system falls back to Pusher automatically via `.env` configuration

---

*End of Documentation*