# Exclusive Content System API Documentation (Flutter)

**Version:** 1.0  
**Last Updated:** July 2026  
**Project:** Ivatan Social Platform

---

## 1. Overview

The **Exclusive Content System** allows creators to lock their posts (Images, Videos, Reels) behind a paywall. Followers must purchase access to view the media. 

### Start-to-End Flow
1. **Enablement:** A Creator requests to enable the "Exclusive Content" feature and pays a one-time setup fee (if applicable). Admin approves/rejects this request.
2. **Creation:** Once enabled, the Creator can upload a post and specify a `price`. The post enters a `pending` state for Admin review.
3. **Approval:** Admin reviews the content and approves it (optionally overriding the platform fee percentage/flat amount).
4. **Purchase:** A user sees the locked post, initiates a purchase via the payment gateway, and completes the transaction.
5. **Wallet Distribution:** Upon successful payment, the funds are split. The Platform Fee is deducted, and the Creator's Share is credited to the Creator's Wallet.
6. **Withdrawal:** Creator can view their wallet balance and transaction history.

---

## 2. Authentication

All APIs require **Laravel Sanctum** authentication.

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

## 4. API Endpoints: Creator Actions

### 4.1 Check Enablement Status
Check if the logged-in creator has the exclusive content feature enabled, pending, or requires payment.

**Endpoint:** `GET /exclusive/enablement-status`

**Response (200 OK):**
```json
{
  "status": "pending",
  "fee_paid": 999.00,
  "payment_status": "pending"
}
```

### 4.2 Request Enablement
Initiate a request to become an exclusive content creator. If a setup fee is active, this returns payment intent details.

**Endpoint:** `POST /exclusive/request-enablement`

**Response (200 OK - When Setup Fee is Active):**
```json
{
  "success": true,
  "enablement": {
    "user_id": 10,
    "fee_paid": 999.00,
    "status": "pending",
    "payment_status": "pending",
    "updated_at": "2026-07-14T12:00:00.000000Z",
    "created_at": "2026-07-14T12:00:00.000000Z",
    "id": 5
  },
  "gateway_order": {
    "id": "ORDS_987654",
    "amount": 99900,
    "currency": "INR"
  },
  "razorpay_order": {
    "id": "ORDS_987654",
    "amount": 99900,
    "currency": "INR"
  }
}
```

**Response (200 OK - When Setup Fee is Free):**
```json
{
  "success": true,
  "message": "Enablement requested. Waiting for admin approval.",
  "data": {
    "user_id": 10,
    "fee_paid": 0,
    "status": "pending",
    "payment_status": "completed"
  }
}
```

### 4.3 Verify Enablement Payment
Verify the setup fee payment from the mobile app via the payment gateway status payload.

**Endpoint:** `POST /exclusive/request-enablement/verify`

**Request Payload:**
```json
{
  "gateway_payload": {
    "providerReferenceId": "T230711152000",
    "code": "PAYMENT_SUCCESS"
  }
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Payment successful. Enablement requested."
}
```

### 4.4 Create Exclusive Post
Create a new post with a price tag. 
*Note: This is an extension of the normal post creation API, but using a dedicated endpoint or passing `price` in the standard endpoint. In our new architecture, it is isolated.*

**Endpoint:** `POST /exclusive/posts`  
**Content-Type:** `multipart/form-data`

**Request Payload:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `type` | string | Yes | `image`, `video`, `reel`, `text` |
| `caption` | string | Yes | Post caption |
| `visibility` | string | Yes | `public`, `private` |
| `media` | file | Yes | The media file |
| `price` | double | Yes | Must be > 0 for exclusive content |

**Response (201 Created):**
```json
{
  "message": "Exclusive Post created successfully and is pending review.",
  "data": {
    "id": 105,
    "caption": "My exclusive photoshoot!",
    "is_exclusive": true,
    "price": "500.00",
    "exclusive_status": "pending"
  }
}
```

### 4.5 Update Exclusive Post Price
Update the price of an existing exclusive post. This will reset the post verification status back to pending.

**Endpoint:** `PUT /exclusive/posts/{post_id}/price`

**Request Payload (JSON):**
```json
{
  "price": 600.00
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Post price updated successfully. Content is pending verification if exclusive.",
  "data": {
    "id": 105,
    "price": "600.00",
    "exclusive_status": "pending"
  }
}
```

### 4.6 Toggle Exclusive Feature
Enable or disable the exclusive content feature for the logged-in creator without submitting a fresh admin request.

**Endpoint:** `POST /exclusive/toggle`

**Request Payload (JSON):**
```json
{
  "is_enabled": true
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "is_enabled": true,
  "message": "Exclusive content feature toggled."
}
```

---

## 5. API Endpoints: Creator Wallet

### 5.1 Get Wallet Balance
Fetch the current available balance for the logged-in creator.

**Endpoint:** `GET /exclusive/wallet/balance`

**Response (200 OK):**
```json
{
  "balance": "1500.00",
  "status": "active"
}
```

### 5.2 Get Wallet Transactions
Fetch the paginated history of wallet credits/debits.

**Endpoint:** `GET /exclusive/wallet/transactions`

**Response (200 OK):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 12,
      "type": "credit",
      "amount": "450.00",
      "description": "Earnings from exclusive post #105",
      "created_at": "2026-07-11T10:00:00.000000Z",
      "buyer": {
        "id": 45,
        "name": "John Doe",
        "username": "johndoe"
      },
      "content": {
        "id": 105,
        "caption": "My exclusive photoshoot!"
      }
    }
  ],
  "total": 1
}
```

---

## 6. API Endpoints: Buyer / User Flow

### 6.1 Initiate Purchase
Call this when the user clicks "Unlock Content". This will return the payment intent details needed for PhonePe/Razorpay.

**Endpoint:** `POST /exclusive/purchase/{post_id}/initiate`

**Error Response (403 Forbidden - Blocked User):**
```json
{
  "message": "You cannot purchase this content."
}
```

**Error Response (400 Bad Request - Already Purchased):**
```json
{
  "message": "You already have active access to this content."
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "transactionId": "TXN_123456789",
  "amount": 500,
  "payment_url": "https://mercury-t2.phonepe.com/transact/..."
}
```

### 6.2 Verify Purchase
Call this after the payment gateway returns success to the mobile app.

**Endpoint:** `POST /exclusive/purchase/verify`

**Request Payload (JSON):**
```json
{
  "purchase_id": 99,
  "gateway_payload": {
    "providerReferenceId": "T230711152000",
    "code": "PAYMENT_SUCCESS"
  }
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Payment successful. Content unlocked."
}
```

### 6.3 Secure Media Access
To fetch the actual image/video file, the app must route through this secure endpoint instead of a direct S3/Storage URL. If the user hasn't paid, this returns a 403.

**Endpoint:** `GET /exclusive/posts/{post_id}/media/{media_id}`

*Usage Note for Flutter:* You must pass the Bearer token in the header of your Image/Video network fetching library (like `CachedNetworkImage` or `video_player`).

**Error Response (403 Forbidden):**
```json
{
  "message": "Unauthorized or blocked. Purchase required."
}
```

**Response (200 OK):**
*Returns raw Binary Stream of the image/video.*

---

## 7. API Endpoints: Admin (Web / App)

Admin APIs are secured behind the `is_admin` middleware.

### 7.1 Wallet Visibility
- `GET /admin/exclusive/wallets` - Lists all creator wallets and their balances.
- `GET /admin/exclusive/wallets/stats` - Returns global platform revenue metrics.
- `GET /admin/exclusive/wallets/transactions` - Global transaction log.

### 7.2 Content Moderation
- `GET /admin/exclusive/pending-content` - List content awaiting approval.
- `POST /admin/exclusive/pending-content/{post_id}/approve` - Approve content (can optionally accept `fee_override_type` and `fee_override_value`).
- `POST /admin/exclusive/pending-content/{post_id}/reject` - Reject content.

---

## 8. Frontend Integration Rules (Crucial for UI/UX)

1. **Pending Status:** If an exclusive post object has `exclusive_status == 'pending'`, the creator should see a banner saying "Awaiting Admin Approval". Other users should NOT see this post in their feed yet.
2. **Blocked Users:** The backend API inherently prevents blocked users from initiating a purchase (Returns `403`). However, ensure the UI hides the "Buy" button if the user block relation is pre-fetched.
3. **Media Fetching:** You cannot use standard Network Image widgets blindly for exclusive content. Ensure your HTTP client injects the Sanctum Token for the `ExclusiveMediaController` routes.
