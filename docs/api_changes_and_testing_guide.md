# API Documentation & Testing Guide

This guide details all API endpoints updated and affected by the recent backend refactorings, including sample request payloads, response structures, headers, and step-by-step testing instructions.

---

## 🔑 Authentication
Most endpoints require Bearer Token Authentication:
- **Header**: `Authorization: Bearer {YOUR_SANCTUM_TOKEN}`
- **Accept**: `application/json`

---

## 1. 🛒 Service Creation & Listing (Free Service Listing Feature)

### 📌 Create a New Service
Allows sellers/creators to publish services.
- **Endpoint**: `POST /api/v1/services`
- **Headers**:
  ```http
  Authorization: Bearer {token}
  Accept: application/json
  Content-Type: multipart/form-data
  ```
- **Request Body (FormData)**:
  | Field | Type | Required | Description |
  | --- | --- | --- | --- |
  | `title` | string | Yes | Service title (e.g., "Full Stack Web Development") |
  | `description` | string | Yes | Detailed description of the service |
  | `price` | numeric | Yes | Price of the service (e.g., `1500.00`) |
  | `discount_price` | numeric | No | Discounted price (optional) |
  | `cover_image` | file | No | Cover image (jpeg, png, jpg) |
  | `images[]` | file array | No | Additional service images |

- **Behavior & Dynamic Setting (`allow_free_service_listing`)**:
  - **When `allow_free_service_listing = 1` (Default)**: Free users without an active paid subscription plan **CAN** publish services successfully.
  - **When `allow_free_service_listing = 0`**: System enforces plan feature check (`sell_services`), returning `403 Forbidden` if user does not have an active paid plan allowing services.

- **Sample Success Response (`201 Created` / `200 OK`)**:
  ```json
  {
    "status": "success",
    "message": "Service created successfully.",
    "data": {
      "id": 42,
      "user_id": 15,
      "title": "Full Stack Web Development",
      "description": "Custom Laravel and Vue.js web application development.",
      "price": "1500.00",
      "discount_price": null,
      "cover_image_url": "https://example.com/storage/services/cover.jpg",
      "created_at": "2026-07-21T13:50:00.000000Z"
    }
  }
  ```

---

## 2. 🔐 Exclusive Content Unlock & Feed Retrieval

### 📌 Initiate Exclusive Content Purchase
Initiates a purchase intent for an exclusive post.
- **Endpoint**: `POST /api/v1/exclusive/purchase/{post_id}/initiate`
- **Headers**:
  ```http
  Authorization: Bearer {token}
  Accept: application/json
  ```
- **URL Parameter**: `post_id` (ID of the exclusive post)
- **Sample Success Response (`200 OK`)**:
  ```json
  {
    "purchase_id": 108,
    "uuid": "ex_pur_9b2a1c0d",
    "final_amount": 499.00,
    "creator_price": 489.22,
    "platform_fee": 9.78,
    "gateway_payment_intent": {
      "order_id": "order_M123456789",
      "amount": 49900,
      "currency": "INR"
    }
  }
  ```

---

### 📌 Verify Exclusive Content Purchase
Verifies gateway payment and grants access atomically with pessimistic locking (`lockForUpdate()`).
- **Endpoint**: `POST /api/v1/exclusive/purchase/verify`
- **Headers**:
  ```http
  Authorization: Bearer {token}
  Content-Type: application/json
  Accept: application/json
  ```
- **Request Payload**:
  ```json
  {
    "purchase_id": 108,
    "gateway_payload": {
      "razorpay_order_id": "order_M123456789",
      "razorpay_payment_id": "pay_P987654321",
      "razorpay_signature": "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855"
    }
  }
  ```
- **Sample Success Response (`200 OK`)**:
  ```json
  {
    "success": true,
    "message": "Payment successful. Content unlocked."
  }
  ```

---

### 📌 Retrieve Exclusive Posts Feed (Unlocked State Check)
Fetches exclusive posts feed. The media URLs are automatically revealed when `has_access` is `true`.
- **Endpoint**: `GET /api/v1/exclusive/posts`
- **Headers**:
  ```http
  Authorization: Bearer {token}
  Accept: application/json
  ```
- **Sample Success Response (`200 OK`)**:
  ```json
  {
    "data": [
      {
        "id": 85,
        "caption": "Exclusive Behind The Scenes Video",
        "is_exclusive": true,
        "price": 499.00,
        "exclusive_status": "approved",
        "has_access": true,
        "is_mine": false,
        "user": {
          "id": 3,
          "name": "Creator Profile",
          "username": "creator_official"
        },
        "media": [
          {
            "id": 204,
            "type": "video",
            "url": "https://example.com/storage/media/exclusive_video.mp4",
            "thumbnail": "https://example.com/storage/media/exclusive_thumb.jpg",
            "mime_type": "video/mp4"
          }
        ]
      }
    ]
  }
  ```

  > 💡 **Note for Unpurchased / Locked Content**:
  > If `has_access` is `false`, `media[].url` and `media[].thumbnail` will be `null` to protect locked content URLs.

---

## 3. ⚙️ Admin Settings Toggle (Free Service Listing Control)

### 📌 Check or Update Free Service Listing Toggle
Admin can toggle whether free users can list services without a subscription.
- **Database Table**: `settings`
- **Key**: `allow_free_service_listing`
- **Values**:
  - `'1'` = Free service listing allowed (Default)
  - `'0'` = Free service listing disabled (Requires paid subscription plan)

---

## 🧪 Step-by-Step Testing Checklist

1. **Service Creation Test (Free User)**:
   - Login as a User without an active subscription plan.
   - Send `POST /api/v1/services` with service data.
   - Confirm HTTP `201 Created` response.

2. **Exclusive Content Purchase Test**:
   - Initiate purchase: `POST /api/v1/exclusive/purchase/{post_id}/initiate`.
   - Verify purchase: `POST /api/v1/exclusive/purchase/verify`.
   - Retrieve post list: `GET /api/v1/exclusive/posts`.
   - Verify `has_access` returns `true` and `media[].url` returns the full media URL.
