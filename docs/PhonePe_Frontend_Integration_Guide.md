# PhonePe Payment Integration — Frontend Integration Guide

> **Gateway:** PhonePe (replaces Razorpay as primary)  
> **Base API URL:** `https://your-api-domain.com/api/v1`  
> **Auth:** All payment endpoints require `Authorization: Bearer <sanctum_token>`

---

## Overview

| Feature | Status |
|---------|--------|
| Ecommerce payments | ✅ Active |
| Ad payments | ✅ Active |
| Subscriptions | ✅ Active (UPI AutoPay Mandate Setup) |
| Webhook (server-to-server) | ✅ Dual endpoints |
| Browser callback (redirect) | ✅ Plain text response in WebView |
| Payment verification | ✅ Via API or auto via webhook |

---

## 1. Payment Flow (Step-by-Step)

### Step 1 — Checkout (Create Order)

```
POST /api/v1/checkout
Headers: Authorization: Bearer <token>
```

**Request:**
```json
{
    "payment_method": "phonepe",
    "shipping_address": {
        "name": "John Doe",
        "phone": "9876543210",
        "address_line1": "123 Main St",
        "city": "Mumbai",
        "state": "Maharashtra",
        "postal_code": "400001"
    }
}
```

**Response (200):**
```json
{
    "success": true,
    "order": {
        "id": 123,
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "total": 500.00,
        "status": "pending",
        "payment_status": "initiated"
    }
}
```

---

### Step 2 — Initiate Payment

> Extracts `redirect_url` from response and opens it in WebView/external browser.

```
POST /api/v1/payment/create
Headers: Authorization: Bearer <token>
```

**Request:**
```json
{
    "order_id": 123
}
```

**Response (200) — Success:**
```json
{
    "success": true,
    "gateway": "phonepe",
    "amount": 500.00,
    "currency": "INR",
    "order_id": 123,
    "redirect_url": "https://pay.phonepe.com/pg/v1/pay?txn=xxx",
    "merchant_transaction_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Response (422) — Validation Error:**
```json
{
    "success": false,
    "message": "The selected order_id is invalid.",
    "errors": {
        "order_id": ["The selected order_id is invalid."]
    }
}
```

**Response (502) — Gateway Error:**
```json
{
    "success": false,
    "error": "Payment gateway error",
    "message": "Failed to create payment on PhonePe",
    "gateway": "phonepe"
}
```

**Backward-compat alias (same as above):**
```
POST /api/v1/payment/razorpay/order
Body: { "order_id": 123 }
```

---

### Step 3 — Open Redirect URL in WebView

| Key | Value |
|-----|-------|
| `redirect_url` | `https://pay.phonepe.com/...` |
| Action | Open in WebView or system browser |

**Flow:**
1. Frontend extracts `redirect_url` from Step 2 response
2. Opens the URL in a WebView (in-app) or external browser
3. User completes payment on PhonePe's hosted payment page
4. PhonePe redirects back to our callback URL inside the WebView
5. Callback returns plain text: `"Payment Successful"` or `"Payment Failed"`
6. Frontend detects the text (or WebView finish event)
7. Frontend calls **Step 4** to verify

---

### Step 4 — Verify Payment (Optional but Recommended)

> Call this after the user returns from the PhonePe payment page.

```
POST /api/v1/payment/verify
Headers: Authorization: Bearer <token>
```

**Request (PhonePe):**
```json
{
    "order_id": 123,
    "merchantTransactionId": "550e8400-e29b-41d4-a716-446655440000"
}
```

> `merchantTransactionId` = the same value returned from Step 2 as `merchant_transaction_id`

**Response (200) — Success:**
```json
{
    "success": true,
    "message": "Payment verified successfully",
    "data": {
        "order_id": 123,
        "status": "paid",
        "gateway": "phonepe",
        "transaction_id": "TXN_PHONEPE_123"
    }
}
```

**Response (200) — Failed:**
```json
{
    "success": false,
    "message": "Payment verification failed",
    "error": "PAYMENT_ERROR"
}
```

**Response (400) — Invalid:**
```json
{
    "success": false,
    "message": "Invalid merchantTransactionId"
}
```

---

### Step 5 — Order Status (Alternative to Step 4)

```
GET /api/v1/orders/{order_id}
Headers: Authorization: Bearer <token>
```

**Response:**
```json
{
    "success": true,
    "order": {
        "id": 123,
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "payment_status": "paid",
        "status": "processing",
        "total": 500.00
    }
}
```

> `payment_status` values: `initiated` → `paid` | `failed`

---

## 2. Webhook (Automatic Processing)

PhonePe sends a server-to-server webhook to both endpoints below **simultaneously**:

| Endpoint | Type |
|----------|------|
| **`POST /api/webhooks/phonepe`** | Primary (PhonePe dashboard configured here) |
| `POST /api/webhooks/payment/phonepe` | Unified (backward compat) |

**Payload (PhonePe → Backend):**
```
Headers:
  X-VERIFY: sha256(base64Response + saltKey)###saltIndex

Body:
{
    "response": "<base64-encoded-JSON>"
}
```

Decoded `response` content:
```json
{
    "success": true,
    "code": "PAYMENT_SUCCESS",
    "data": {
        "merchantId": "MERCH123",
        "merchantTransactionId": "550e8400-e29b-41d4-a716-446655440000",
        "transactionId": "TXN_PHONEPE_123",
        "amount": 50000
    }
}
```

### Architectural Enhancements (Security & Performance)

1. **Webhook Deduplication**: Webhooks now perform a Cache-based deduplication check using the `merchantTransactionId`. If PhonePe delivers duplicate webhook notifications, the system automatically skips duplicate queue jobs.
2. **Direct Status Fallback**: When the browser redirects to `/payment/callback/phonepe`, the system attempts to find the corresponding database order. If missing (such as sandbox/test panel transactions), it executes a direct gateway verification call to verify success/failure before redirecting.
3. **Automatic Subscription Persistence**: Subscription initiation (`POST /api/v1/profiles/{id}/subscriptions/initiate`) now persists the subscription as `pending` with `gateway_subscription_id` immediately, ensuring webhook charging events never 404.

**Backend processes automatically:** Updates order status to `paid`, dispatches `ProcessOrderPayment` job, sends notification.

**Flow summary:** Even if frontend never calls verify endpoint, payment will be processed automatically via webhook within seconds.

---

## 3. Ad Payments

### Get Pending Order

```
GET /api/v1/ads/{ad_id}/pending-order
Headers: Authorization: Bearer <token>
```

**Response (same structure as ecommerce initiate):**
```json
{
    "success": true,
    "gateway": "phonepe",
    "redirect_url": "https://pay.phonepe.com/...",
    "merchant_transaction_id": "ad_45_1700000000"
}
```

### Verify Ad Payment

```
POST /api/v1/ads/payments/verify
Headers: Authorization: Bearer <token>
```

**Request:**
```json
{
    "merchantTransactionId": "ad_45_1700000000"
}
```

---

## 4. Subscription Payments (UPI AutoPay Mandates)

### Initiate Subscription Setup

```
POST /api/v1/profiles/{profile_id}/subscriptions/initiate
Headers: Authorization: Bearer <token>
```

**Request:**
```json
{
    "subscription_plan_id": 2
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Subscription initiated successfully.",
    "data": {
        "requires_payment": true,
        "gateway": "phonepe",
        "gateway_subscription_id": "SUB_648ef894a8210",
        "razorpay_key": "",
        "redirect_url": "https://mercury-uat.phonepe.com/transact/pgv2?token=...",
        "plan": {
            "id": 2,
            "name": "Premium Plan",
            "price": 499.00,
            "currency": "INR"
        }
    }
}
```

**Flow:**
1. Frontend makes the initiate request.
2. Extracts `redirect_url` and loads it in WebView/system browser.
3. User authorizes the UPI AutoPay mandate with their UPI PIN.
4. PhonePe redirects the user back to the redirect callback URL.
5. PhonePe sends a server-to-server webhook containing `paymentFlow.merchantSubscriptionId` with `COMPLETED` state which activates the subscription.

---

## 5. Key Differences from Razorpay

| Aspect | Razorpay (Old) | PhonePe (New) |
|--------|---------------|---------------|
| **Payment page** | Razorpay checkout JS SDK (in-page) | PhonePe hosted page (redirect) |
| **Initiate response** | `{ order_id, amount }` | `{ redirect_url, merchant_transaction_id }` |
| **Verify payload** | `razorpay_payment_id + razorpay_order_id + razorpay_signature` | `merchantTransactionId` only |
| **Verification** | Client-side signature + server-side | Server-side API call to PhonePe |
| **Subscriptions** | ✅ Supported | ✅ Supported (UPI AutoPay via Web Redirect) |
| **Webhook signature** | `razorpay_signature` HMAC | `X-VERIFY` header (sha256) |
| **Callback** | Redirect with `razorpay_payment_id` query params | POST with `code` + `merchantTransactionId` |
| **Amount format** | Rupees (float) | Paise (integer * 100) |
| **Credentials source** | `.env` (old) / DB settings (new) | DB settings only (`payment.phonepe.*`) |

---

## 6. Error Codes

| HTTP Code | Meaning | Frontend Action |
|-----------|---------|-----------------|
| 200 | Success / Verified | Proceed to next screen |
| 400 | Bad request / Invalid params | Show validation error |
| 401 | Unauthenticated | Redirect to login |
| 422 | Validation error | Show field errors |
| 502 | Gateway error (PhonePe down) | Show "Try again later" |
| 504 | Gateway timeout | Show "Try again later" |

---

## 6. Frontend Integration Checklist

- [ ] Step 1: Call `POST /api/v1/checkout` with `"payment_method": "phonepe"`
- [ ] Step 2: Extract `order_id` → call `POST /api/v1/payment/create`
- [ ] Step 3: Extract `redirect_url` → open in WebView
- [ ] Step 4: After WebView closes / callback text received → call `POST /api/v1/payment/verify`
- [ ] Step 5: On verify success → show success screen
- [ ] Step 6: On verify failure → call `GET /api/v1/orders/{id}` to double-check actual status
- [ ] Remove: All Razorpay SDK references, `razorpay_payment_id`/`razorpay_order_id`/`razorpay_signature` from verify payloads
- [ ] Replace: `merchantTransactionId` in verify payload for PhonePe

---

## 7. Environment

| Setting | Config Key | Where to set |
|---------|-----------|-------------|
| Active gateway | `payment.active_gateway` | Admin panel → Settings → Payment |
| Merchant ID | `payment.phonepe.key` | Admin panel (encrypted) |
| Salt Key | `payment.phonepe.secret` | Admin panel (encrypted) |
| Salt Index | `payment.phonepe.webhook_secret` | Admin panel (encrypted) |
| Environment | `payment.phonepe.env` | Admin panel → `sandbox` or `production` |

> **Production URL:** `https://api.phonepe.com/apis/hermes`
> **Sandbox URL:** `https://api-preprod.phonepe.com/apis/pg-sandbox`
