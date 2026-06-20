# Profile & Subscription Integration Guide for Frontend Developers

This document explains how to handle multi-profile flows, profile creation, switching, and subscription purchases using the API endpoints.

---

## 1. High-Level Concepts

* **Multi-Profile System**: A single user account can have multiple sub-profiles (`personal`, `employer`, `seller` (shown as `ecommerce` on frontend), `music`, `creator`).
* **Active Profile**: The session state on the frontend (e.g., "Switched to Employer Mode").
* **Profile-Level Subscription**: Subscriptions are bound to a specific `Profile` (by `profile_id`), **not** to the User directly.
* **Subscription Purchase Restrictions**:
  1. A profile can have at most **one active paid subscription** at a time.
  2. Every profile is auto-assigned a **Default Free** plan upon creation. This free plan **does not** block the user from initiating or purchasing a paid plan.
  3. To upgrade/downgrade between different paid plans on the same profile, the current subscription must first be cancelled/expired, or handled manually.
  4. You can buy plans for other profiles without switching your current active session profile, but you must pass the correct `profile_id` in the API payload.

---

## 2. API Flow Chart

```mermaid
graph TD
    A[User wants E-Commerce Plan] --> B{Does E-Commerce Profile exist?}
    B -- No -- > C[Call POST /api/profiles to create E-Commerce Profile]
    C --> D[Get profile_id from response]
    B -- Yes --> E[Get profile_id from GET /api/profiles]
    D --> F[Call POST /api/profiles/profileId/subscriptions/initiate]
    E --> F
    F --> G{Requires Payment?}
    G -- Yes (Paid Plan) --> H[Initialize Razorpay/PhonePe Gateway using redirect_url/keys]
    G -- No (Free Plan) --> I[Subscription activated directly]
    H --> J[Redirect user to payment gateway OR open SDK]
    J --> K[Callback/Webhook verifies payment and marks subscription as active]
```

---

## 3. Detailed API Reference & Implementation

All endpoints require the standard header:
`Authorization: Bearer <TOKEN>`

### 3.1 Fetch User's Profiles & Check Existence
Before initiating a plan, check if the target profile exists and retrieve its ID.

* **Endpoint**: `GET /api/v1/profiles`
* **Request Headers**:
  ```json
  {
    "Accept": "application/json",
    "Authorization": "Bearer <sanctum_token>"
  }
  ```
* **Response**:
  ```json
  {
    "success": true,
    "message": "Profiles retrieved successfully.",
    "data": {
      "profiles": [
        {
          "id": 12,
          "type": "personal",
          "is_active": true
        },
        {
          "id": 15,
          "type": "seller", 
          "is_active": false
        }
      ],
      "active_profile": {
        "id": 12,
        "type": "personal"
      }
    }
  }
  ```
  *(Note: Frontend `ecommerce` type is mapped to `seller` in backend).*

---

### 3.2 Create the Target Profile (If Not Exists)
If the user does not have a profile of that type, create it first.

* **Endpoint**: `POST /api/v1/profiles`
* **Payload**:
  ```json
  {
    "type": "ecommerce",
    "profile_sub_type": "product" // Allowed for ecommerce: product, service, both
  }
  ```
  *(Note: Setting profile_sub_type to "both" requires a paid subscription first).*
* **Response**:
  ```json
  {
    "success": true,
    "message": "Profile created successfully. Pending admin approval.",
    "data": {
      "profile": {
        "id": 26,
        "type": "seller",
        "status": "pending_approval"
      }
    }
  }
  ```

---

### 3.3 Fetch Available Subscription Plans
Fetch all plans or filter by profile type to present options to the user.

* **Endpoint**: `GET /api/subscription-plans`
* **Query Parameters**:
  * `profile_type`: Filter plans by type (`personal`, `employer`, `ecommerce`, `music`, `creator`)
* **Response**:
  ```json
  {
    "success": true,
    "message": "Subscription plans retrieved successfully.",
    "data": {
      "plans": [
        {
          "id": 3,
          "profile_type": "seller",
          "name": "E-Commerce Premium",
          "price": "499.00",
          "currency": "INR",
          "duration_days": 30,
          "gateway_plan_id": "plan_seller_prem_123"
        }
      ]
    }
  }
  ```

---

### 3.4 Initiate Subscription Purchase
Initiate the subscription payment transaction for the target profile (even if the user is switched to another profile).

* **Endpoint**: `POST /api/v1/profiles/{profileId}/subscriptions/initiate`
* **Path Parameter**: `{profileId}` = ID of the profile you want to buy the plan for.
* **Payload**:
  ```json
  {
    "subscription_plan_id": 3
  }
  ```
* **Response (Paid Plan)**:
  ```json
  {
    "success": true,
    "message": "Subscription initiated successfully.",
    "data": {
      "requires_payment": true,
      "gateway": "phonepe", 
      "gateway_subscription_id": "sub_12345abcdef",
      "razorpay_key": "rzp_test_...",
      "redirect_url": "https://merch.phonepe.com/pay/...",
      "plan": {
        "id": 3,
        "name": "E-Commerce Premium",
        "price": 499.00,
        "currency": "INR"
      }
    }
  }
  ```
* **Actions on App**:
  * **Requires Payment = false**: Free plan has been activated successfully. Inform user and refresh profiles.
  * **Requires Payment = true**: 
    1. If `gateway` is `phonepe`, open the provided `redirect_url` in a Webview/Browser to let the user complete the payment.
    2. If `gateway` is `razorpay`, open the Razorpay SDK using the `gateway_subscription_id` and `razorpay_key`.

---

### 3.5 Verify Payment (Optional Check)
After completing payment inside the SDK or web redirection, verify it programmatically (though the webhook also processes it in the background).

* **Endpoint**: `POST /api/v1/payment/verify`
* **Payload**:
  ```json
  {
    "order_id": 25,
    "merchantTransactionId": "OMO26062016..."
  }
  ```
* **Response**:
  ```json
  {
    "success": true,
    "message": "Payment verified successfully. Order is being processed.",
    "order_id": 25
  }
  ```

---

## 4. Switching Active Session Profile
To toggle the active profile session on the app (e.g., Switching from Personal Mode to Employer/Seller Mode):

* **Endpoint**: `POST /api/v1/profiles/switch`
* **Payload**:
  ```json
  {
    "to_profile_type": "ecommerce", // personal, employer, ecommerce, music, creator
    "profile_sub_type": "product", // Optional (product, service, both)
    "notes": "I want to switch to seller profile." // Optional
  }
  ```
* **Response**:
  ```json
  {
    "success": true,
    "message": "Approval is pending.",
    "data": {
      "switch_request": {
        "id": 5,
        "user_id": 35,
        "to_profile_type": "seller",
        "status": "pending"
      }
    }
  }
  ```
  *(Note: Certain profiles require admin approval before they can be switched to, depending on backend settings).*
