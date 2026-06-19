# PhonePe Payment Integration — Frontend Integration Guide

> **Gateway:** PhonePe (replaces Razorpay as primary)  
> **Base API URL:** `https://www.ivatan.in/api` or `https://your-api-domain.com/api` (all versioned endpoints use `/v1`)  
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

```http
POST /api/v1/checkout
Headers: 
  Authorization: Bearer <token>
  Content-Type: application/json
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

```http
POST /api/v1/payment/create
Headers:
  Authorization: Bearer <token>
  Content-Type: application/json
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

> [!NOTE]
> **Backward Compatibility:** The endpoint `POST /api/v1/payment/razorpay/order` is maintained as an alias for backward compatibility. It accepts the same body: `{ "order_id": 123 }`.

---

### Step 3 — Open Redirect URL in WebView

| Key | Value |
|-----|-------|
| `redirect_url` | `https://pay.phonepe.com/...` |
| Action | Open in WebView (Mobile App) or system browser |

#### **Flow & Interception Details for Frontend Developers:**
1. Frontend extracts the `redirect_url` from Step 2.
2. Load this URL inside an in-app WebView or open in the system browser.
3. The user will select a UPI app, scan a QR code, or enter credentials to pay.
4. **WebView Interception Rule:** The frontend MUST monitor WebView page loads and intercept any URL matching the callback pattern:
   * **Pattern:** `https://<your-domain>/payment/callback/phonepe` (e.g., `https://www.ivatan.in/payment/callback/phonepe`)
5. **Handling the Callback:**
   * When the WebView hits this URL, the backend processes the redirect.
   * The page will return a plain text response body:
     * **Success:** `"Payment Successful"` (HTTP 200)
     * **Failure/Cancel:** `"Payment Failed"` (HTTP 200)
   * The frontend should read this string or intercept the URL state, close the WebView, and proceed to verify the payment status via API.

---

### Step 4 — Verify Payment (Optional but Recommended)

> Call this after the user returns from the PhonePe payment page.

```http
POST /api/v1/payment/verify
Headers:
  Authorization: Bearer <token>
  Content-Type: application/json
```

**Request (PhonePe):**
```json
{
    "order_id": 123,
    "merchantTransactionId": "550e8400-e29b-41d4-a716-446655440000"
}
```

> [!TIP]
> The `merchantTransactionId` corresponds to the UUID of the order (or the `merchant_transaction_id` returned in Step 2).

**Response (200) — Success:**
```json
{
    "success": true,
    "message": "Payment verified successfully. Order is being processed.",
    "order_id": 123
}
```

**Response (422) — Failed:**
```json
{
    "success": false,
    "message": "Payment verification failed.",
    "error": "Payment verification failed or was declined by PhonePe."
}
```

---

### Step 5 — Order Status (Alternative/Fallback)

```http
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

> **Possible `payment_status` values:** `initiated` | `paid` | `failed`

---

## 2. Webhook (Automatic Server-to-Server Verification)

PhonePe sends automated server-to-server webhook callbacks directly to the backend. The frontend does not need to handle webhook notifications, but can query the Order Status endpoint to update the UI once the webhook resolves.

| Webhook Route | Description |
|---------------|-------------|
| **`POST /api/webhooks/phonepe`** | Primary Webhook Endpoint |
| **`POST /api/webhooks/payment/phonepe`** | Secondary (unified backward compat) |

---

## 3. Ad Payments

### Get Pending Order & Pay

```http
GET /api/v1/ads/{ad_id}/pending-order
Headers: Authorization: Bearer <token>
```

**Response:**
```json
{
    "success": true,
    "gateway": "phonepe",
    "redirect_url": "https://pay.phonepe.com/...",
    "merchant_transaction_id": "ad_45_1700000000"
}
```

### Verify Ad Payment

```http
POST /api/v1/ads/payments/verify
Headers:
  Authorization: Bearer <token>
  Content-Type: application/json
```

**Request Body:**
```json
{
    "merchantTransactionId": "ad_45_1700000000"
}
```

---

## 4. Subscription Payments (UPI AutoPay Mandates)

### Initiate Subscription Setup

```http
POST /api/v1/profiles/{profile_id}/subscriptions/initiate
Headers:
  Authorization: Bearer <token>
  Content-Type: application/json
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

### Mandate Setup Lifecycle:
1. **Redirect User:** 
   * Extract `redirect_url` from the response.
   * Open this URL in the WebView or external browser.
2. **UPI Mandate Authorization:**
   * The user **must** select their UPI app (PhonePe, GPay, Paytm, etc.) and complete the AutoPay setup inside their UPI application.
   * > [!WARNING]
   * > **Sandbox/UAT Constraint:** When testing in UAT/Sandbox environment, scanning the fallback default QR Code will execute a standard checkout transaction instead of a subscription setup. Users must choose the UPI intent/app options to authenticate the AutoPay mandate simulator.
3. **Web Redirect Callback Interception:**
   * Once setup completes, PhonePe redirects the WebView to our callback route:
     * **Success Pattern:** `https://<domain>/payment/callback/phonepe?code=PAYMENT_SUCCESS&merchantTransactionId=ORD_...`
     * **Cancel Pattern:** `https://<domain>/payment/callback/phonepe?code=PAYMENT_CANCELLED&merchantTransactionId=ORD_...`
   * The WebView page returns plain text `"Payment Successful"` or `"Payment Failed"`. The frontend should detect this, close the WebView, and sync state.
4. **Subscription Status Verification:**
   * Query the profile subscription status endpoint to fetch the updated state:
     * `GET /api/v1/profiles/{id}/subscriptions/active`

---

## 5. Key Differences from Razorpay

| Aspect | Razorpay (Old) | PhonePe (New) |
|--------|---------------|---------------|
| **Payment page** | Razorpay checkout JS SDK (in-page) | PhonePe hosted page (redirect) |
| **Initiate response** | `{ order_id, amount }` | `{ redirect_url, merchant_transaction_id }` |
| **Verify payload** | `razorpay_payment_id + razorpay_order_id + razorpay_signature` | `order_id` + `merchantTransactionId` |
| **Verification** | Client-side signature + server-side | Server-side API call to PhonePe |
| **Subscriptions** | ✅ Supported | ✅ Supported (UPI AutoPay via Web Redirect Setup) |
| **Amount format** | Rupees (float) | Paise (integer * 100) |
| **Credentials source** | `.env` | DB settings only (`payment.phonepe.*`) |

---

## 6. Error Codes

| HTTP Code | Meaning | Frontend Action |
|-----------|---------|-----------------|
| 200 | Success / Verified | Proceed to next screen |
| 400 | Bad request / Invalid params | Show validation error |
| 401 | Unauthenticated | Redirect to login / Refresh token |
| 422 | Validation error | Show field errors |
| 502 | Gateway error (PhonePe down) | Show "Try again later" |
| 504 | Gateway timeout | Show "Try again later" |

---

## 7. Frontend Integration Checklist

- [ ] **Step 1:** Call `POST /api/v1/checkout` with `"payment_method": "phonepe"`.
- [ ] **Step 2:** Extract `order_id` → call `POST /api/v1/payment/create`.
- [ ] **Step 3:** Extract `redirect_url` → open in WebView.
- [ ] **Step 4:** Intercept callback route `/payment/callback/phonepe` in WebView.
- [ ] **Step 5:** Detect plain text body response (`"Payment Successful"` vs `"Payment Failed"`).
- [ ] **Step 6:** Call `POST /api/v1/payment/verify` to confirm payment and process the order on the backend.
- [ ] **Step 7 (Fallback):** If verify times out, call `GET /api/v1/orders/{id}` to fetch final status.
- [ ] **Cleanup:** Remove Razorpay checkout SDK scripts and references.
