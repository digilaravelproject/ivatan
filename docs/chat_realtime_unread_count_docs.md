# Chat Real-time & Unread Count API Documentation

---

## 1. Get Chats (Inbox) Listing

### Endpoint
`GET /api/v1/chats`

Retrieves a paginated list of private and group chats for the authenticated user, excluding live chat groups.

#### Headers
* `Accept: application/json`
* `Authorization: Bearer <token>`

#### Response Example
```json
{
  "success": true,
  "data": {
    "chats": {
      "data": [
        {
          "id": 7,
          "uuid": "b8dde83c-44ad-4133-a9f4-96bc04ba0768",
          "type": "private",
          "name": "Awntika",
          "avatar": "https://www.ivatan.in/storage/...jpg",
          "is_online": false,
          "is_admin": false,
          "unread_count": 3,
          "last_message": {
            "id": 142,
            "chat_id": 7,
            "content": "hii",
            "message_type": "text",
            "created_at": "2026-06-24T08:25:15.000Z"
          },
          "updated_at": "2026-06-24T08:25:15.000Z"
        }
      ]
    }
  }
}
```

---

## 2. Get Live Chat Groups Listing

### Endpoint
`GET /api/v1/chats?filter=live_groups`

Retrieves only the live chat groups created by administrators where the user is a participant. Response structure is identical to the standard inbox.

---

## 3. Real-Time Events (Broadcasting)

To update the inbox listing and chat messages without manual refreshing, subscribe to the following broadcast channels:

### 3.1 Private User Channel (User Inbox & Global Notifications)
* **Channel Name:** `private-user.{userId}`
* **Events:**
  * **`message.sent`:** Broadcasted when any participant sends a message to any of the user's active chats. 
    * *Usage:* Increment the `unread_count` for the matching `chat_id` and update the `last_message` in the UI list.
  * **`message.read`:** Broadcasted when the other user marks messages as read.
    * *Usage:* Reset the `unread_count` to `0` or decrease it in real-time.

#### Message Sent Broadcast Payload (`message.sent`):
```json
{
  "id": 142,
  "chat_id": 7,
  "content": "hii",
  "message_type": "text",
  "attachment_url": null,
  "is_mine": false,
  "created_at": "2026-06-24T08:25:15.000Z",
  "sender": {
    "id": 35,
    "name": "Yash",
    "avatar": "https://www.ivatan.in/storage/..."
  }
}
```

#### Message Read Broadcast Payload (`message.read`):
```json
{
  "chat_id": 7,
  "last_read_message_id": 142,
  "read_by": 33,
  "read_at": "2026-06-24T08:25:20.000Z"
}
```

### 3.2 Chat Presence Channel (Inside Active Chat Screen)
* **Channel Name:** `presence-chat.{chatId}`
* **Events:**
  * **`message.sent`:** Broadcasted for active room participants.
  * **`message.read`:** Broadcasted inside the active room to mark messages as read.
