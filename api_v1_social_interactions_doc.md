# API Documentation: Social Interactions (v1)

This documentation provides details for integrating the Bookmark, Block, Interested, and Not Interested features into the Ivatan platform.

## 🔐 Authentication & Headers
All endpoints below require a valid **Sanctum API Token**.
- **Header**: `Authorization: Bearer {token}`
- **Accept**: `application/json`

---

## 🔖 1. Bookmark APIs

### 1.1 Toggle Bookmark
**Endpoint**: `/api/v1/posts/{id}/bookmark`  
**Method**: `POST`  
**Purpose**: Saves a post to the user's collection or removes it if already saved (Idempotent toggle).

#### Request
- **Path Parameter**: `id` (integer) - The ID of the post to bookmark.
- **Payload**: No body required.

#### Response (Success - 200 OK)
```json
{
    "success": true,
    "message": "Post added to bookmarks.",
    "is_bookmarked": true
}
```

#### Response (Post Not Found - 404 Not Found)
```json
{
    "success": false,
    "message": "Post not found."
}
```

---

### 1.2 Get My Bookmarks (Collections)
**Endpoint**: `/api/v1/user/bookmarks`  
**Method**: `GET`  
**Purpose**: Retrieves the authenticated user's "Saved Posts" collection.

#### Query Parameters
- `type` (optional): Filter by `post`, `video`, `reel`, or `carousel`.
- `per_page` (optional): Number of records per page (default: 15, max: 50).
- `page` (optional): Page number for pagination.

#### Response (Success - 200 OK)
```json
{
    "success": true,
    "data": [
        {
            "bookmark_id": 12,
            "bookmarked_at": "2026-03-30T08:00:00Z",
            "bookmarked_human": "2 minutes ago",
            "post": {
                "id": 45,
                "type": "video",
                "caption": "Beautiful sunset...",
                "media": [...],
                "stats": {
                    "like_count": 150,
                    "is_saved": true,
                    "is_liked": false
                }
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 1,
        "last_page": 1,
        "has_more": false
    }
}
```

---

## 🚫 2. Blocking APIs

### 2.1 Toggle Block/Unblock User
**Endpoint**: `/api/v1/users/{id}/block`  
**Method**: `POST`  
**Purpose**: Blocks or unblocks a specific user.
- **On Block**: Both users automatically unfollow each other. Content is hidden from feeds. Chatting is disabled.
- **On Unblock**: Content visibility and interaction capabilities are restored.

#### Request
- **Path Parameter**: `id` (integer) - The ID of the user to block/unblock.

#### Response (Blocked - 200 OK)
```json
{
    "success": true,
    "message": "User blocked successfully. They will no longer see your content or interact with you.",
    "is_blocked": true,
    "code": "BLOCKED"
}
```

#### Response (Unblocked - 200 OK)
```json
{
    "success": true,
    "message": "User unblocked successfully. You can now see their content and interact.",
    "is_blocked": false,
    "code": "UNBLOCKED"
}
```

---

### 2.2 Get Blocked Users List
**Endpoint**: `/api/v1/user/blocked-users`  
**Method**: `GET`  
**Purpose**: Displays a list of users currently blocked by the authenticated user.

#### Response (Success - 200 OK)
```json
{
    "success": true,
    "data": [
        {
            "id": 89,
            "name": "John Doe",
            "username": "johndoe",
            "avatar": "https://.../avatar.jpg",
            "is_verified": true,
            "blocked_at": "2026-03-30T07:45:00Z",
            "blocked_human": "1 hour ago"
        }
    ],
    "pagination": { ... }
}
```

---

## ✨ 3. Preference APIs (Algorithm Control)

### 3.1 Mark Interested
**Endpoint**: `/api/v1/posts/{id}/interested`  
**Method**: `POST`  
**Purpose**: Signals the algorithm to show more content similar to this post.

#### Response (Success - 200 OK)
```json
{
    "success": true,
    "message": "Post marked as interested.",
    "preference": "interested"
}
```

---

### 3.2 Mark Not Interested
**Endpoint**: `/api/v1/posts/{id}/not-interested`  
**Method**: `POST`  
**Purpose**: Immediately hides this post from the feed and reduces content frequency from this author and similar categories.

#### Response (Success - 200 OK)
```json
{
    "success": true,
    "message": "Post marked as not interested.",
    "preference": "not_interested"
}
```

---

### 3.3 Remove Preference
**Endpoint**: `/api/v1/posts/{id}/preference`  
**Method**: `DELETE`  
**Purpose**: Resets any "Interested" or "Not Interested" signal for a post.

#### Response (Success - 200 OK)
```json
{
    "success": true,
    "message": "Preference removed."
}
```

---

## 🔄 4. Changes in Existing APIs

### 4.1 Post Object Modifcations
The standard `Post` resource (returned in all feeds/search) has been updated:
- **New Field**: `stats.is_saved` (boolean) - Indicates if the current user has bookmarked this post.
- **New Field**: `stats.is_blocked` (boolean) - Indicates if a block relationship exists with the author (bidirectional).

### 4.2 Profile API (`/api/v1/posts/user/{username}`)
If you visit a profile of a user that you have blocked (or who has blocked you):
- **Status**: `200 OK` (Profile loads for UX consistency).
- **Flag in meta**: `is_blocked: true`.
- **Content**: `posts` array will be empty `[]`.
- **Message**: Returns "You have blocked this user" or "This user is not available".

### 4.3 Chat APIs
- `/api/v1/chats/private` (Create Chat): Will return `422 Unprocessable Entity` if trying to chat with a blocked user.
- `/api/v1/chats/{id}/messages` (Send Message): Will fail with an exception (handled as error) if a block exists.

---

## 🧠 5. Edge Cases & Logic

| Scenario | Expected Behavior |
|----------|-------------------|
| **Blocking a User** | The `followers` record for both users is immediately deleted. Following counts for both users are recalculated. |
| **Double Blocking** | Toggling block on an already blocked user results in an **Unblock** action. |
| **Self Actions** | Blocking yourself or marking your own posts as "Not Interested" will return `422` validation errors. |
| **Feed Filtering** | Once a post is marked "Not Interested", it will **vanish** from all feeds (`for-you`, `trending`, `images`) for that user upon refresh. |
| **Similar Content** | Authors of "Not Interested" posts are deprioritized. Their content is pushed to the bottom of the feed. |

---

## ⚠️ 6. Error Handling

### Common Error Responses
```json
{
    "success": false,
    "message": "Human readable error message.",
    "code": "ERROR_CONSTANT" // Optional
}
```

| Error Code | Status | Reason |
|------------|--------|--------|
| `NOT_FOUND` | 404 | Post or User ID does not exist. |
| `SELF_ACTION_FORBIDDEN` | 422 | You tried to block yourself. |
| `UNAUTHORIZED` | 401 | Missing or invalid token. |
| `SERVER_ERROR` | 500 | Database or internal logic failure. |

---

## 💡 Frontend Integration Tips
1. **State Persistence**: When `POST /bookmark` returns `is_bookmarked: true`, update the UI icon locally to "saved".
2. **Immediate Removal**: When `POST /not-interested` is called, the frontend should immediately remove the post card from the list.
3. **Block Confirmation**: Always show a confirmation dialog before blocking, as it deletes follow relationships.
