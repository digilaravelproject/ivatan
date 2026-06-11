# API Response Changelog — Profiles & Subscriptions

> **Date:** 2026-06-04
> **For:** Frontend Integration
> **Base URL:** `/api`

---

## New Public Endpoints

### GET `/api/profile-types`
**Available profile types with metadata.**

```json
{
  "status": true,
  "message": "Profile types retrieved successfully.",
  "data": {
    "types": [
      {
        "type": "personal",
        "label": "Personal Profile",
        "description": "Default personal profile with basic features.",
        "is_default": true,
        "requires_approval": false,
        "has_subscription": true
      },
      {
        "type": "employer",
        "label": "Employer Profile",
        "description": "Post job openings and manage recruitment.",
        "is_default": false,
        "requires_approval": true,
        "has_subscription": false
      },
      {
        "type": "seller",
        "label": "Product & Service Seller",
        "description": "Sell products, services, or both.",
        "is_default": false,
        "requires_approval": true,
        "has_subscription": true,
        "seller_types": ["products", "services", "both"]
      },
      {
        "type": "music",
        "label": "Music Playlist Profile",
        "description": "Create and manage music playlists.",
        "is_default": false,
        "requires_approval": true,
        "has_subscription": false
      },
      {
        "type": "creator",
        "label": "Content Creator Profile",
        "description": "Upload content, manage monetization.",
        "is_default": false,
        "requires_approval": true,
        "has_subscription": true
      }
    ]
  }
}
```

---

### GET `/api/subscription-plans`
**List available plans. Optional filter: `?profile_type=seller|personal|creator`**

```json
{
  "status": true,
  "message": "Subscription plans retrieved successfully.",
  "data": {
    "plans": [
      {
        "id": 1,
        "profile_type": "seller",
        "name": "Basic Seller",
        "slug": "seller-basic",
        "description": "Start selling with basic features.",
        "price": "0.00",
        "currency": "INR",
        "duration_days": 36500,
        "features": ["List up to 10 products", "Basic analytics", "Standard support"],
        "is_active": true,
        "is_default": true,
        "sort_order": 1
      }
    ],
    "filters": { "profile_type": null }
  }
}
```

**Plan counts per type:** personal=5, seller=3, creator=2. Total = 10.

---

### GET `/api/subscription-plans/{id}`
**Single plan details.**

```json
{
  "status": true,
  "message": "Plan details retrieved successfully.",
  "data": { "plan": { ... } }
}
```

---

## New Authenticated Endpoints (`/api/v1`)

### GET `/api/v1/profiles`
**List all user profiles with their active subscription.**

```json
{
  "status": true,
  "message": "Profiles retrieved successfully.",
  "data": {
    "profiles": [
      {
        "id": 1,
        "user_id": 5,
        "type": "personal",
        "status": "active",
        "is_active": true,
        "is_default": true,
        "approved_at": null,
        "created_at": "2026-06-04T10:00:00Z",
        "seller_details": null,
        "employer_details": null,
        "music_details": null,
        "creator_details": null,
        "active_subscription": {
          "id": 1,
          "status": "active",
          "plan": { "id": 6, "name": "Free", "price": "0.00" }
        }
      }
    ],
    "active_profile": { ... }
  }
}
```

---

### GET `/api/v1/profiles/active`
**Currently active profile only.**

```json
{
  "status": true,
  "message": "Active profile retrieved successfully.",
  "data": {
    "profile": { ... }
  }
}
```

---

### GET `/api/v1/profiles/{id}`
**Single profile with full subscription history.**

```json
{
  "status": true,
  "message": "Profile retrieved successfully.",
  "data": {
    "profile": {
      "id": 2,
      "type": "seller",
      "seller_details": {
        "seller_type": "products",
        "business_name": "My Shop",
        ...
      },
      "active_subscription": { ... },
      "subscriptions": [
        { "id": 1, "status": "active", "plan": { ... } }
      ]
    }
  }
}
```

---

### POST `/api/v1/profiles`
**Rate Limited: 10/1min. Create additional profile.**

**Request:**
```json
{
  "type": "seller",
  "seller_type": "products",
  "business_name": "Shop Name",
  "business_description": "..."
}
```

**Response (201):**
```json
{
  "status": true,
  "message": "Profile created successfully. Pending admin approval.",
  "data": {
    "profile": {
      "id": 3,
      "type": "seller",
      "status": "pending_approval",
      "is_active": false,
      "seller_details": { "seller_type": "products", ... }
    }
  }
}
```

**Error (422) — Seller "both" without subscription:**
```json
{
  "status": false,
  "message": "A subscription is required to sell both products and services. Please purchase a subscription first.",
  "errors": []
}
```

---

### DELETE `/api/v1/profiles/{id}`
**Delete non-personal profile.**

**Response:**
```json
{
  "status": true,
  "message": "Profile deleted successfully.",
  "data": []
}
```

**Error (422) — Personal profile:**
```json
{
  "status": false,
  "message": "The default Personal profile cannot be deleted.",
  "errors": []
}
```

---

### POST `/api/v1/profiles/switch`
**Request profile switch.**

**Request:**
```json
{
  "to_profile_type": "employer",
  "notes": "I want to hire people"
}
```

**Response (201):**
```json
{
  "status": true,
  "message": "Approval is pending.",
  "data": {
    "switch_request": {
      "id": 1,
      "user_id": 5,
      "from_profile_id": 1,
      "to_profile_id": 4,
      "to_profile_type": "employer",
      "status": "pending",
      "user_notes": "I want to hire people",
      "created_at": "2026-06-04T10:30:00Z"
    }
  }
}
```

---

### GET `/api/v1/profile-switch-requests`
**User's switch request history.**

```json
{
  "status": true,
  "message": "Switch requests retrieved successfully.",
  "data": {
    "switch_requests": [
      {
        "id": 1,
        "status": "approved",
        "to_profile_type": "employer",
        "from_profile": { "id": 1, "type": "personal" },
        "to_profile": { "id": 4, "type": "employer", "status": "active" },
        "approver": { "id": 1, "name": "Admin" },
        "approved_at": "2026-06-04T11:00:00Z"
      }
    ]
  }
}
```

---

### Profile Detail Endpoints

| Method | Endpoint | Response |
|--------|----------|----------|
| GET | `/api/v1/profiles/{id}/seller-details` | `{ seller_details: { seller_type, business_name, ... } }` |
| PUT | `/api/v1/profiles/{id}/seller-details` | Update seller type, business info |
| GET | `/api/v1/profiles/{id}/employer-details` | `{ employer_details: { company_name, industry, ... } }` |
| GET | `/api/v1/profiles/{id}/music-details` | `{ music_details: { artist_name, genre, ... } }` |
| GET | `/api/v1/profiles/{id}/creator-details` | `{ creator_details: { channel_name, ... } }` |

**PUT `/api/v1/profiles/{id}/seller-details` request:**
```json
{
  "seller_type": "both"
}
```

**Error (422) — Changing to "both" without subscription:**
```json
{
  "status": false,
  "message": "A subscription is required to sell both products and services. Please purchase a subscription first.",
  "errors": []
}
```

---

### POST `/api/v1/profiles/{id}/subscriptions`
**Purchase/assign a subscription.**

**Request:**
```json
{
  "subscription_plan_id": 3,
  "payment_method": "free|razorpay",
  "razorpay_payment_id": "pay_abc123",
  "razorpay_order_id": "order_abc123",
  "razorpay_subscription_id": "sub_abc123"
}
```

**Response (201):**
```json
{
  "status": true,
  "message": "Subscription purchased successfully.",
  "data": {
    "subscription": {
      "id": 10,
      "user_id": 5,
      "profile_id": 2,
      "status": "active",
      "starts_at": "2026-06-04T12:00:00Z",
      "ends_at": "2026-07-04T12:00:00Z",
      "plan": {
        "id": 3,
        "name": "Pro Seller",
        "price": "499.00",
        "duration_days": 30,
        "features": ["Unlimited products", "Sell both types", ...]
      }
    }
  }
}
```

---

### GET `/api/v1/profiles/{id}/subscriptions/active`
**Active subscription for profile.**

```json
{
  "status": true,
  "message": "Active subscription retrieved successfully.",
  "data": {
    "subscription": { "id": 10, "status": "active", "plan": { ... } }
  }
}
```

### GET `/api/v1/profiles/{id}/subscriptions/history`
**All past subscriptions.**

```json
{
  "status": true,
  "message": "Subscription history retrieved successfully.",
  "data": {
    "history": [
      { "id": 5, "status": "expired", "plan": { ... } }
    ]
  }
}
```

### POST `/api/v1/subscriptions/{id}/cancel`
**Cancel active subscription.**

**Request:**
```json
{ "reason": "Not needed" }
```

**Response:**
```json
{
  "status": true,
  "message": "Subscription cancelled successfully.",
  "data": {
    "subscription": { "id": 10, "status": "cancelled", "cancelled_at": "..." }
  }
}
```

---

## Admin Endpoints (`/api/v1/admin`)

### GET `/api/v1/admin/profile-switch-requests`
**List pending requests. Filters: `?profile_type=employer&per_page=20`**

```json
{
  "status": true,
  "message": "Pending switch requests retrieved successfully.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 5,
        "to_profile_type": "employer",
        "status": "pending",
        "user_notes": "...",
        "created_at": "...",
        "user": { "id": 5, "name": "John", "email": "john@test.com", "username": "john" },
        "from_profile": { "id": 1, "type": "personal" },
        "to_profile": { "id": 4, "type": "employer", "status": "pending_approval" }
      }
    ],
    "total": 1
  }
}
```

### POST `/api/v1/admin/profile-switch-requests/{id}/approve`
**Approve or reject.**

**Request:**
```json
{
  "status": "approved",
  "admin_notes": "Verified. Approved."
}
```

**Response (approve):**
```json
{
  "status": true,
  "message": "Profile switch approved successfully.",
  "data": {
    "switch_request": { "id": 1, "status": "approved", "approved_by": 1, ... },
    "new_active_profile": { "id": 4, "type": "employer", "status": "active", "is_active": true },
    "assigned_subscription": {
      "id": 11,
      "subscription_plan_id": 1,
      "status": "active",
      "plan_name": "Employer Default"
    }
  }
}
```

**Response (reject):**
```json
{
  "status": true,
  "message": "Profile switch request rejected.",
  "data": {
    "switch_request": { "id": 1, "status": "rejected", "admin_notes": "..." }
  }
}
```

---

## Webhook

### POST `/api/webhooks/razorpay`
**Razorpay subscription events (no auth, signature verified).**

Handles:
- `subscription.charged` → activates subscription
- `subscription.cancelled` → marks as cancelled
- `subscription.completed` → marks as expired
- `payment.failed` → marks as past_due

---

## New Error Codes

| HTTP | Scenario |
|------|----------|
| 403 | Profile is pending approval / suspended |
| 409 | Duplicate profile type |
| 422 | Seller "both" without subscription |
| 422 | Cannot delete personal profile |
| 422 | Cannot switch to same type |
| 422 | Already have a pending switch request |
| 422 | Plan incompatible with profile type |
| 422 | Profile already has active subscription |

---

## Frontend Integration Guide

### Registration Flow
1. **POST `/api/auth/register`** — creates User + Personal profile automatically.
2. If user wants another profile type, redirect to **Profile Creation** after login.

### Profile Creation Flow
1. Fetch types via **GET `/api/profile-types`**.
2. Show type selector to user.
3. For **seller**: if user selects "both", check subscription status first.
4. POST `/api/v1/profiles` to create.
5. If `requires_approval`, show "Pending approval" message.

### Profile Switch Flow
1. User clicks "Switch Profile".
2. Show available types (exclude current type).
3. POST `/api/v1/profiles/switch` with `to_profile_type`.
4. Show "Approval is pending." banner.
5. Poll **GET `/api/v1/profile-switch-requests`** to check status.
6. When status changes to "approved", refresh page — new profile is active.

### Subscription Purchase Flow
1. Fetch plans: **GET `/api/subscription-plans?profile_type=seller`**.
2. Show plan cards with features.
3. User selects plan → **POST `/api/v1/profiles/{id}/subscriptions`**.
4. If plan is free → 201 response, subscription active immediately.
5. If plan is paid → redirect to Razorpay checkout.
6. After payment → Razorpay webhook activates subscription.

### Admin Flow
1. Admin dashboard lists pending requests: **GET `/api/v1/admin/profile-switch-requests`**.
2. Admin reviews user details, old/new profile types.
3. Admin clicks Approve/Reject: **POST `/api/v1/admin/profile-switch-requests/{id}/approve`**.
4. On approve: old profile deactivated, new profile activated, subscription auto-assigned.
