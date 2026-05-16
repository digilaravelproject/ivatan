# User History API Documentation

**Base URL:** `/api/v1`

**Auth:** Bearer Token (Sanctum) via `Authorization: Bearer <token>` header.

**Pagination:** All endpoints use cursor-based pagination. Pass `meta.next_cursor` as `?cursor=` in the next request.

---

## 1. Like History

### GET `/api/v1/history/likes`

Fetch the authenticated user's like history.

**Query Parameters:**

| Param | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `per_page` | integer | 20 | 50 | Items per page |
| `cursor` | string | - | - | Cursor from previous response `meta.next_cursor` |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "entity_type": "reel",
      "entity_id": 42,
      "preview": {
        "caption": "First 50 characters of caption...",
        "thumbnail": "https://cdn.example.com/thumb.jpg"
      },
      "created_at": "2026-05-16T10:30:00Z",
      "created_human": "2 hours ago"
    }
  ],
  "meta": {
    "next_cursor": "eyJpZCI6MjUsIl9wb2ludHMiOnRydWV9",
    "per_page": 20,
    "has_more": true
  }
}
```

> `entity_type` values: `reel`, `video`, `post`, `deleted` (when entity was removed).

---

## 2. Comment History

### GET `/api/v1/history/comments`

Fetch the authenticated user's comment history.

**Query Parameters:**

| Param | Type | Default | Max |
|-------|------|---------|-----|
| `per_page` | integer | 20 | 50 |
| `cursor` | string | - | - |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "body": "Great post!",
      "entity_type": "UserPost",
      "entity_id": 42,
      "parent_id": null,
      "created_at": "2026-05-16T10:30:00Z",
      "created_human": "2 hours ago"
    }
  ],
  "meta": {
    "next_cursor": "eyJpZCI6...",
    "per_page": 20,
    "has_more": false
  }
}
```

> `parent_id` is `null` for top-level comments, or contains the parent comment ID for replies.

---

## 3. Video View History

### GET `/api/v1/history/video-views`

Fetch the authenticated user's video view history with post type filtering.

**Query Parameters:**

| Param | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `filter` | string | `both` | - | `reels` \| `long_video` \| `both` |
| `per_page` | integer | 20 | 50 | |
| `cursor` | string | - | - | |

**Filter Values:**

| Value | Returns |
|-------|---------|
| `reels` | Only reel-type posts |
| `long_video` | Only long-form video posts |
| `both` | Both reels and long videos (default) |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": [
    {
      "id": 10,
      "post_type": "reel",
      "entity_id": 42,
      "preview": {
        "thumbnail": "https://cdn.example.com/thumb.jpg",
        "caption": "My awesome reel"
      },
      "created_at": "2026-05-16T10:30:00Z",
      "created_human": "2 hours ago"
    }
  ],
  "meta": {
    "next_cursor": "eyJpZCI6...",
    "per_page": 20,
    "has_more": true
  }
}
```

**Error `422` for invalid filter:**

```json
{
  "success": false,
  "message": "Invalid filter. Use: reels, long_video, or both."
}
```

---

## 4. Product Purchase History

### GET `/api/v1/history/purchases`

Fetch the authenticated user's product purchase history (paid/delivered orders with products).

**Query Parameters:**

| Param | Type | Default | Max |
|-------|------|---------|-----|
| `per_page` | integer | 20 | 50 |
| `cursor` | string | - | - |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": [
    {
      "order_id": 100,
      "order_uuid": "abc-123-def",
      "total_amount": 299.99,
      "status": "delivered",
      "items": [
        {
          "title": "Wireless Headphones",
          "quantity": 1,
          "price": 299.99
        }
      ],
      "created_at": "2026-05-16T10:30:00Z"
    }
  ],
  "meta": {
    "next_cursor": "eyJpZCI6...",
    "per_page": 20,
    "has_more": false
  }
}
```

---

## 5. Service History

### GET `/api/v1/history/services`

Fetch the authenticated user's service purchase history (paid/delivered orders with services).

**Query Parameters:**

| Param | Type | Default | Max |
|-------|------|---------|-----|
| `per_page` | integer | 20 | 50 |
| `cursor` | string | - | - |

**Response `200 OK`:**

```json
{
  "success": true,
  "data": [
    {
      "order_id": 101,
      "order_uuid": "ghi-789-jkl",
      "total_amount": 149.99,
      "status": "paid",
      "items": [
        {
          "title": "Website Design",
          "quantity": 1,
          "price": 149.99
        }
      ],
      "created_at": "2026-05-16T10:30:00Z"
    }
  ],
  "meta": {
    "next_cursor": "eyJpZCI6...",
    "per_page": 20,
    "has_more": false
  }
}
```

---

## Error Responses

### `401 Unauthorized` (No token / expired token)

```json
{
  "message": "Unauthenticated."
}
```

### `422 Unprocessable Entity` (Invalid filter parameter)

```json
{
  "success": false,
  "message": "Invalid filter. Use: reels, long_video, or both."
}
```

### `500 Internal Server Error`

```json
{
  "success": false,
  "message": "Failed to fetch [history_type] history."
}
```

---

## Cursor Pagination Usage

All endpoints return cursor-based pagination in the `meta` object.

**First request:**
```
GET /api/v1/history/likes?per_page=20
```

**Next page:**
```
GET /api/v1/history/likes?per_page=20&cursor=eyJpZCI6MjUsIl9wb2ludHMiOnRydWV9
```

**Stop condition:** When `meta.has_more` is `false` or `meta.next_cursor` is `null`, there are no more pages.

---

## Admin Web Routes (Blade UI)

These are accessible via the admin panel at `/admin/users/{user}/history/{type}`.

| Route Name | URL | Description |
|------------|-----|-------------|
| `admin.users.history.likes` | `/admin/users/{user}/history/likes` | View user's likes |
| `admin.users.history.comments` | `/admin/users/{user}/history/comments` | View user's comments |
| `admin.users.history.video-views` | `/admin/users/{user}/history/video-views?filter=` | View user's video views |
| `admin.users.history.purchases` | `/admin/users/{user}/history/purchases` | View user's purchases |
| `admin.users.history.services` | `/admin/users/{user}/history/services` | View user's services |

All admin routes require `auth` and `is_admin` middleware. Filter options (reels/long_video/both) available for video-views.
