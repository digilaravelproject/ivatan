# User Search API Documentation

This document describes the API endpoint for searching users by name or username.

---

## Endpoint

### Search Users
Finds users by name or username with typo tolerance (if search driver is enabled) or database fallback.

* **URL:** `/api/v1/users/search`
* **Method:** `GET`
* **Headers:** 
  * `Authorization: Bearer <token>`
  * `Accept: application/json`
* **Query Params:**
  * `q` [string, required] - Search query. Must be at least 2 characters.
  * `per_page` [integer, optional] - Results per page. Default: `20`.
* **Success Response:**
  * **Status Code:** `200 OK`
    ```json
    {
      "success": true,
      "message": "Users search results fetched successfully.",
      "data": [
        {
          "id": 5,
          "username": "janedoe",
          "name": "Jane Doe",
          "avtar": "https://your-site.com/storage/avatars/janedoe.png",
          "is_verified": false,
          "is_followed_by_auth_user": false,
          "is_auth_user": false
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
* **Error Response:**
  * **Status Code:** `422 Unprocessable Entity` (query too short)
    ```json
    {
      "success": false,
      "message": "Search query must be at least 2 characters long.",
      "data": []
    }
    ```

---

## Privacy & Block Filtering

This endpoint automatically respects the bidirectional block list relationships. If User A has blocked User B (or vice versa), neither will appear in each other's search results.
