# User Blocking API Documentation

This document describes the API endpoints for user blocking, unblocking, and retrieving the list of blocked users. 

Additionally, once a user is blocked, bidirectional isolation is strictly enforced across chats, post feeds, comments, notifications, stories, search, contact sync, and profiles.

---

## Endpoints

### 1. Toggle Block/Unblock
Toggles the block status of a specific user.

* **URL:** `/api/v1/users/{id}/block`
* **Method:** `POST`
* **Headers:** 
  * `Authorization: Bearer <token>`
  * `Accept: application/json`
* **URL Params:**
  * `id` [integer] - The ID of the target user.
* **Success Responses:**
  * **Status Code:** `200 OK` (when blocking)
    ```json
    {
      "success": true,
      "message": "User blocked successfully.",
      "is_blocked": true,
      "code": "BLOCKED"
    }
    ```
  * **Status Code:** `200 OK` (when unblocking)
    ```json
    {
      "success": true,
      "message": "User unblocked successfully. You can now see their content and interact.",
      "is_blocked": false,
      "code": "UNBLOCKED"
    }
    ```
* **Error Responses:**
  * **Status Code:** `422 Unprocessable Entity` (attempting to block yourself)
    ```json
    {
      "success": false,
      "message": "You cannot block yourself.",
      "is_blocked": false,
      "code": "SELF_ACTION_FORBIDDEN"
    }
    ```
  * **Status Code:** `404 Not Found` (target user not found)
    ```json
    {
      "success": false,
      "message": "User not found.",
      "is_blocked": false,
      "code": "NOT_FOUND"
    }
    ```

---

### 2. List Blocked Users
Retrieves the paginated list of users blocked by the currently authenticated user.

* **URL:** `/api/v1/users/blocked` (alias `/api/v1/user/blocked-users` for backwards compatibility)
* **Method:** `GET`
* **Headers:** 
  * `Authorization: Bearer <token>`
  * `Accept: application/json`
* **Query Params:**
  * `per_page` [integer, optional] - Default: `20`, Max: `50`.
* **Success Response:**
  * **Status Code:** `200 OK`
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 5,
          "name": "Jane Doe",
          "username": "janedoe",
          "avatar": "https://your-site.com/storage/avatars/janedoe.png",
          "is_verified": false,
          "blocked_at": "2026-07-10T12:30:43.000000Z",
          "blocked_human": "2 minutes ago"
        }
      ],
      "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 1,
        "last_page": 1,
        "has_more": false
      }
    }
    ```

---

## Bidirectional Privacy Isolation Rules

Once User A blocks User B (or vice versa):
1. **Profile Access:** Attempting to view the blocked user's profile (`GET /api/v1/users/{username}`) returns a `404 Not Found` response.
2. **Direct Messages (DMs):**
   - Active private chat threads are hidden from the inbox/chat listing (`GET /api/v1/chats`).
   - Trying to open a chat or send a message throws a `403 Forbidden` response.
3. **Post Feeds:** Posts from the blocked user are excluded from all feeds (trending, home, video, reels feeds).
4. **Stories & Highlights:** Stories of the blocked user are excluded from feeds, and tapping on the profile stories is blocked.
5. **Comments:** Comments and replies from the blocked user are hidden. Furthermore, posting comments/replies on each other's content is blocked.
6. **Followers:** Follow relations are automatically severed on block. Re-following is blocked.
7. **Contact Sync:** Contact sync does not match phone numbers belonging to blocked users.
8. **Notifications:** Historical notifications from the blocked user are hidden, and any new interactions are completely muted.
