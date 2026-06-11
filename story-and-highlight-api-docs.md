# Stories & Highlights API — Documentation for Flutter

> **Version**: 1.0  
> **Last Updated**: 2026-06-11  
> **Stack**: Laravel 12 + Sanctum + Spatie MediaLibrary

---

## Table of Contents

1. [Authentication & Headers](#authentication--headers)
2. [Story API Endpoints](#story-api-endpoints)
   - [2.1 Get Stories Feed](#21-get-stories-feed)
   - [2.2 Get My Stories](#22-get-my-stories)
   - [2.3 Get User Stories](#23-get-user-stories)
   - [2.4 Create Story](#24-create-story)
   - [2.5 Get Story Details](#25-get-story-details)
   - [2.6 Delete Story](#26-delete-story)
   - [2.7 Mark Story as Viewed](#27-mark-story-as-viewed)
   - [2.8 Toggle Like Story](#28-toggle-like-story)
3. [Highlight API Endpoints](#highlight-api-endpoints)
   - [3.1 Get User Highlights](#31-get-user-highlights)
   - [3.2 Get Highlight Details](#32-get-highlight-details)
   - [3.3 Create Highlight](#33-create-highlight)
   - [3.4 Add Story to Highlight](#34-add-story-to-highlight)
   - [3.5 Remove Story from Highlight](#35-remove-story-from-highlight)
   - [3.6 Delete Highlight](#36-delete-highlight)
4. [Data Models (Flutter / Dart)](#data-models-flutter--dart)

---

## Authentication & Headers

All requests require **Bearer token authentication** (Sanctum).

**Headers:**
```http
Authorization: Bearer <sanctum_token>
Accept: application/json
Content-Type: application/json
```

*Note: For endpoints that accept file uploads (e.g. `POST /api/v1/stories` and `POST /api/v1/stories/highlights`), use `multipart/form-data` instead of `application/json`.*

---

## Story API Endpoints

### 2.1 Get Stories Feed

Returns active stories from users the authenticated user follows first, followed by other active public stories.

* **Method:** `GET`
* **URL:** `/api/v1/stories/feed`
* **Query Parameters:**
  * `page` (int, optional): Page number for pagination (Default: `1`)

**Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 18,
      "user_id": 42,
      "caption": "Morning coffee!",
      "type": "image",
      "media_url": "https://www.ivatan.in/storage/media/stories/coffee.jpg",
      "views_count": 14,
      "likes_count": 5,
      "is_liked": true,
      "is_viewed": true,
      "expires_at": "2026-06-12T10:30:00.000000Z",
      "created_at": "2026-06-11T10:30:00.000000Z",
      "user": {
        "id": 42,
        "name": "John Doe",
        "username": "johndoe",
        "avatar": "https://www.ivatan.in/storage/avatars/john.jpg"
      }
    }
  ]
}
```

---

### 2.2 Get My Stories

Returns all active stories posted by the currently authenticated user.

* **Method:** `GET`
* **URL:** `/api/v1/stories/me`

**Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 20,
      "user_id": 19,
      "caption": "My new project work!",
      "type": "image",
      "media_url": "https://www.ivatan.in/storage/media/stories/work.jpg",
      "views_count": 3,
      "likes_count": 0,
      "is_liked": false,
      "is_viewed": false,
      "expires_at": "2026-06-12T11:00:00.000000Z",
      "created_at": "2026-06-11T11:00:00.000000Z"
    }
  ]
}
```

---

### 2.3 Get User Stories

Returns active stories of a specific user profile by their username. Respects profile privacy settings.

* **Method:** `GET`
* **URL:** `/api/v1/stories/user/{username}`

**Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 18,
      "user_id": 42,
      "caption": "Morning coffee!",
      "type": "image",
      "media_url": "https://www.ivatan.in/storage/media/stories/coffee.jpg",
      "views_count": 14,
      "likes_count": 5,
      "is_liked": true,
      "is_viewed": true,
      "expires_at": "2026-06-12T10:30:00.000000Z",
      "created_at": "2026-06-11T10:30:00.000000Z"
    }
  ]
}
```

---

### 2.4 Create Story

Uploads and publishes a new story. Supports images and videos.

* **Method:** `POST`
* **URL:** `/api/v1/stories`
* **Headers:** `Content-Type: multipart/form-data`
* **Request Payload (Form-Data):**
  * `file` (file, required): Image or Video file (max 10MB/50MB depending on server configuration).
  * `caption` (string, optional): Story caption text.
  * `expires_at` (datetime, optional): Expiration timestamp. (Defaults to 24 hours from creation).

**Response `201 Created`:**
```json
{
  "success": true,
  "message": "Story published successfully.",
  "data": {
    "id": 21,
    "user_id": 19,
    "caption": "Beautiful evening!",
    "type": "image",
    "media_url": "https://www.ivatan.in/storage/media/stories/evening.jpg",
    "views_count": 0,
    "likes_count": 0,
    "is_liked": false,
    "is_viewed": false,
    "expires_at": "2026-06-12T11:45:00.000000Z",
    "created_at": "2026-06-11T11:45:00.000000Z"
  }
}
```

---

### 2.5 Get Story Details

Retrieves details of a single story by ID.

* **Method:** `GET`
* **URL:** `/api/v1/stories/{id}`

**Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "id": 18,
    "user_id": 42,
    "caption": "Morning coffee!",
    "type": "image",
    "media_url": "https://www.ivatan.in/storage/media/stories/coffee.jpg",
    "views_count": 14,
    "likes_count": 5,
    "is_liked": true,
    "is_viewed": true,
    "expires_at": "2026-06-12T10:30:00.000000Z",
    "created_at": "2026-06-11T10:30:00.000000Z"
  }
}
```

---

### 2.6 Delete Story

Permanently deletes a story by ID. The user must be the owner of the story.

* **Method:** `DELETE`
* **URL:** `/api/v1/stories/{id}`

**Response `200 OK`:**
```json
{
  "success": true,
  "message": "Deleted."
}
```

---

### 2.7 Mark Story as Viewed

Records a unique view for a story. (Views are tracked using user session data).

* **Method:** `POST`
* **URL:** `/api/v1/stories/{id}/view`

**Response `200 OK`:**
```json
{
  "success": true,
  "message": "Story view registered."
}
```

---

### 2.8 Toggle Like Story

Toggles the like status of a story. Updates counters safely without extending expiration.

* **Method:** `POST`
* **URL:** `/api/v1/stories/{id}/like`

**Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "is_liked": true,
    "count": 6
  }
}
```

---

## Highlight API Endpoints

### 3.1 Get User Highlights

Returns all story highlights created by a specific user.

* **Method:** `GET`
* **URL:** `/api/v1/stories/highlights/user/{username}`

**Response `200 OK`:**
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "title": "Summer Vibe",
      "cover_url": "https://www.ivatan.in/storage/media/covers/summer.jpg",
      "created_at": "2026-06-10T12:00:00.000000Z",
      "stories": [
        {
          "id": 14,
          "caption": "Sunset!",
          "type": "image",
          "media_url": "https://www.ivatan.in/storage/media/stories/sunset.jpg"
        }
      ]
    }
  ]
}
```

---

### 3.2 Get Highlight Details

Retrieves details of a single highlight, including all associated stories.

* **Method:** `GET`
* **URL:** `/api/v1/stories/highlights/{id}`

**Response `200 OK`:**
```json
{
  "success": true,
  "data": {
    "id": 5,
    "title": "Summer Vibe",
    "cover_url": "https://www.ivatan.in/storage/media/covers/summer.jpg",
    "created_at": "2026-06-10T12:00:00.000000Z",
    "stories": [
      {
        "id": 14,
        "caption": "Sunset!",
        "type": "image",
        "media_url": "https://www.ivatan.in/storage/media/stories/sunset.jpg"
      }
    ]
  }
}
```

---

### 3.3 Create Highlight

Creates a new highlight. Can upload a custom cover photo and sync initial stories immediately.

* **Method:** `POST`
* **URL:** `/api/v1/stories/highlights`
* **Headers:** `Content-Type: multipart/form-data`
* **Request Payload (Form-Data):**
  * `title` (string, required): Highlight name (max 100 chars).
  * `cover_media` (file, optional): Image for custom cover (max 5MB).
  * `story_ids` (array, optional): List of active story IDs to link during creation (e.g. `story_ids[] = 14`, `story_ids[] = 18`).

**Response `201 Created`:**
```json
{
  "success": true,
  "message": "Highlight created.",
  "data": {
    "id": 6,
    "title": "Throwbacks",
    "cover_url": "https://www.ivatan.in/storage/media/covers/default.jpg",
    "created_at": "2026-06-11T11:34:00.000000Z",
    "stories": []
  }
}
```

---

### 3.4 Add Story to Highlight

Links an active story to an existing highlight.

* **Method:** `POST`
* **URL:** `/api/v1/stories/highlights/{highlightId}/{storyId}`

**Response `200 OK`:**
```json
{
  "success": true,
  "message": "Story added to highlight.",
  "data": {
    "id": 5,
    "title": "Summer Vibe",
    "cover_url": "https://www.ivatan.in/storage/media/covers/summer.jpg",
    "created_at": "2026-06-10T12:00:00.000000Z",
    "stories": [
      {
        "id": 14,
        "caption": "Sunset!",
        "type": "image",
        "media_url": "https://www.ivatan.in/storage/media/stories/sunset.jpg"
      },
      {
        "id": 18,
        "caption": "Morning coffee!",
        "type": "image",
        "media_url": "https://www.ivatan.in/storage/media/stories/coffee.jpg"
      }
    ]
  }
}
```

---

### 3.5 Remove Story from Highlight

Unlinks a story from a highlight.

* **Method:** `DELETE`
* **URL:** `/api/v1/stories/highlights/{highlightId}/{storyId}`

**Response `200 OK`:**
```json
{
  "success": true,
  "message": "Story removed from highlight.",
  "data": {
    "id": 5,
    "title": "Summer Vibe",
    "cover_url": "https://www.ivatan.in/storage/media/covers/summer.jpg",
    "created_at": "2026-06-10T12:00:00.000000Z",
    "stories": [
      {
        "id": 18,
        "caption": "Morning coffee!",
        "type": "image",
        "media_url": "https://www.ivatan.in/storage/media/stories/coffee.jpg"
      }
    ]
  }
}
```

---

### 3.6 Delete Highlight

Deletes an entire highlight and detaches all linked stories safely. Associated covers are cleaned up.

* **Method:** `DELETE`
* **URL:** `/api/v1/stories/highlights/{id}`

**Response `200 OK`:**
```json
{
  "success": true,
  "message": "Highlight deleted successfully."
}
```

---

## Data Models (Flutter / Dart)

### UserPreview
```dart
class UserPreview {
  final int id;
  final String name;
  final String username;
  final String avatar;

  UserPreview({
    required this.id,
    required this.name,
    required this.username,
    required this.avatar,
  });

  factory UserPreview.fromJson(Map<String, dynamic> json) {
    return UserPreview(
      id: json['id'],
      name: json['name'] ?? '',
      username: json['username'] ?? '',
      avatar: json['avatar'] ?? '',
    );
  }
}
```

### StoryPreview
```dart
class StoryPreview {
  final int id;
  final String? caption;
  final String type; // "image" | "video"
  final String mediaUrl;

  StoryPreview({
    required this.id,
    this.caption,
    required this.type,
    required this.mediaUrl,
  });

  factory StoryPreview.fromJson(Map<String, dynamic> json) {
    return StoryPreview(
      id: json['id'],
      caption: json['caption'],
      type: json['type'] ?? 'image',
      mediaUrl: json['media_url'] ?? '',
    );
  }
}
```

### Story
```dart
class Story {
  final int id;
  final int userId;
  final String? caption;
  final String type; // "image" | "video"
  final String mediaUrl;
  final int viewsCount;
  final int likesCount;
  final bool isLiked;
  final bool isViewed;
  final String expiresAt;
  final String createdAt;
  final UserPreview? user; // Only included in feed response

  Story({
    required this.id,
    required this.userId,
    this.caption,
    required this.type,
    required this.mediaUrl,
    required this.viewsCount,
    required this.likesCount,
    required this.isLiked,
    required this.isViewed,
    required this.expiresAt,
    required this.createdAt,
    this.user,
  });

  factory Story.fromJson(Map<String, dynamic> json) {
    return Story(
      id: json['id'],
      userId: json['user_id'],
      caption: json['caption'],
      type: json['type'] ?? 'image',
      mediaUrl: json['media_url'] ?? '',
      viewsCount: json['views_count'] ?? 0,
      likesCount: json['likes_count'] ?? 0,
      isLiked: json['is_liked'] ?? false,
      isViewed: json['is_viewed'] ?? false,
      expiresAt: json['expires_at'] ?? '',
      createdAt: json['created_at'] ?? '',
      user: json['user'] != null ? UserPreview.fromJson(json['user']) : null,
    );
  }
}
```

### Highlight
```dart
class Highlight {
  final int id;
  final String title;
  final String coverUrl;
  final String createdAt;
  final List<StoryPreview> stories;

  Highlight({
    required this.id,
    required this.title,
    required this.coverUrl,
    required this.createdAt,
    required this.stories,
  });

  factory Highlight.fromJson(Map<String, dynamic> json) {
    var storyList = json['stories'] as List? ?? [];
    return Highlight(
      id: json['id'],
      title: json['title'] ?? '',
      coverUrl: json['cover_url'] ?? '',
      createdAt: json['created_at'] ?? '',
      stories: storyList.map((e) => StoryPreview.fromJson(e)).toList(),
    );
  }
}
```
