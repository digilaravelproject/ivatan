# Ivatan API Documentation

**Base URL:** `http://localhost:8000/api`  
**Content-Type:** `application/json` (unless multipart/form-data noted)  
**Auth:** Bearer Token via `Authorization: Bearer {token}`

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Account Management](#2-account-management)
3. [Profile Management](#3-profile-management)
4. [Admin — Profile Approval](#4-admin--profile-approval)
5. [Subscription Management](#5-subscription-management)
6. [Profile Configuration](#6-profile-configuration)
7. [Ecommerce — Products](#7-ecommerce--products)
8. [Ecommerce — Services](#8-ecommerce--services)
9. [Webhook](#9-webhook)

---

## Response Format

All API responses follow this structure:

**Success (200/201):**
```json
{
    "status": true,
    "message": "Operation successful.",
    "data": { ... }
}
```

**Client Error (400/401/404/409/422):**
```json
{
    "status": false,
    "message": "Error description.",
    "errors": []
}
```

**Server Error (500):**
```json
{
    "status": false,
    "message": "An internal server error occurred.",
    "errors": []
}
```

---

## 1. Authentication

### 1.1 Register

Creates a new user account with a personal profile.

**POST** `/auth/register`

**Headers:** `Accept: application/json`

**Request Body:**
```json
{
    "name": "Aarav Mehta",
    "email": "aarav.mehta@example.com",
    "phone": "9876543210",
    "username": "aarav_mehta",
    "password": "password@123",
    "password_confirmation": "password@123",
    "date_of_birth": "1995-06-15",
    "occupation": "Software Engineer",
    "interests": ["Technology", "Music"]
}
```

| Field | Type | Rules |
|-------|------|-------|
| name | string | required, max:255 |
| email | string | required, email, max:255, unique:users |
| phone | string | required, unique:users |
| username | string | required, max:50, unique:users |
| password | string | required, min:8 |
| password_confirmation | string | required, must match password |
| date_of_birth | string (date) | required |
| occupation | string | nullable, max:255 |
| interests | array | nullable — accepts IDs `[1, 2]` or names `["Technology"]` |

**Success (201):**
```json
{
    "status": true,
    "message": "User registered successfully.",
    "data": {
        "user": { ... },
        "token": "1|abc123..."
    }
}
```

**Error (422):**
```json
{
    "status": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

---

### 1.2 Login

Authenticates a user by email, phone, or username.

**POST** `/auth/login`

**Headers:** `Accept: application/json`

**Request Body** (by email):
```json
{
    "email": "aarav.mehta@example.com",
    "password": "password@123"
}
```

**Request Body** (by username):
```json
{
    "username": "aarav_mehta",
    "password": "password@123"
}
```

**Request Body** (by phone):
```json
{
    "phone": "9876543210",
    "password": "password@123"
}
```

| Field | Type | Rules |
|-------|------|-------|
| email | string | nullable (one of email/phone/username required) |
| phone | string | nullable |
| username | string | nullable |
| password | string | required, min:8 |

**Success (200):**
```json
{
    "status": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "token": "2|def456..."
    }
}
```

**Error — Invalid credentials (401):**
```json
{
    "status": false,
    "message": "Invalid credentials.",
    "errors": []
}
```

**Error — No identifier provided (422):**
```json
{
    "status": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": ["Please provide at least email, phone, or username to login."]
    }
}
```

---

### 1.3 Logout

Revokes the current Sanctum token.

**DELETE** `/v1/auth/logout`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Logged out successfully.",
    "data": []
}
```

---

### 1.4 Google Login

**POST** `/auth/google-login`

**Headers:** `Accept: application/json`

---

### 1.5 Mobile Login

**POST** `/auth/mobile_login`

**Headers:** `Accept: application/json`

---

## 2. Account Management

### 2.1 Check Username Availability

**POST** `/check-username`

**Headers:** `Accept: application/json`

**Request Body:**
```json
{
    "username": "aarav_mehta"
}
```

**Success (200) — Available:**
```json
{
    "status": true,
    "message": "Username is available.",
    "data": []
}
```

**Error (400) — Taken:**
```json
{
    "status": false,
    "message": "Username is already taken.",
    "errors": []
}
```

---

### 2.2 Update Profile

Updates authenticated user's profile fields.

**POST** `/v1/auth/update`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "Aarav M.",
    "bio": "Senior Full Stack Developer",
    "gender": "male",
    "language_preference": "hi",
    "account_privacy": "private",
    "hide_email": true,
    "hide_phone": false,
    "interests": [1, 3]
}
```

| Field | Type | Rules |
|-------|------|-------|
| name | string | sometimes, max:255 |
| email | string | sometimes, email, max:255, unique (ignores self) |
| phone | string | sometimes, unique (ignores self) |
| username | string | sometimes, max:50, unique (ignores self) |
| password | string | sometimes, min:8 |
| bio | string | nullable, max:1000 |
| gender | string | nullable, in:male,female,other,prefer_not_to_say |
| language_preference | string | nullable, max:10 |
| account_privacy | string | nullable, in:public,private |
| messaging_privacy | string | nullable, in:everyone,followers,none |
| hide_email | boolean | sometimes |
| hide_phone | boolean | sometimes |
| is_employer | boolean | sometimes |
| is_seller | boolean | sometimes |
| settings | object | nullable |
| email_notification_preferences | object | nullable |
| profile_photo | file | image, mimes:jpeg,png,jpg,gif,webp, max:2048 (multipart) |
| interests | array | nullable |

**Success (200):**
```json
{
    "status": true,
    "message": "Profile updated successfully.",
    "data": {
        "user": { ... }
    }
}
```

---

### 2.3 Get User by Username

**GET** `/v1/users/{username}`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "User details retrieved successfully.",
    "data": {
        "user": {
            "id": 1,
            "uuid": "...",
            "username": "aarav_mehta",
            "name": "Aarav Mehta",
            "email": "aarav.mehta@example.com",
            "phone": "9876543210",
            "is_following": false,
            "is_follower": false,
            "chat_id": null,
            "is_mine": true,
            "followers_count": 0,
            "following_count": 0,
            "posts_count": 0,
            "bio": "...",
            "profile_photo_url": "...",
            "is_verified": false,
            "interests": [...],
            "created_at": "...",
            "updated_at": "..."
        }
    }
}
```

**Error (404):**
```json
{
    "status": false,
    "message": "User not found.",
    "errors": []
}
```

---

### 2.4 Delete Account (Soft Delete)

Schedules the authenticated user's account for permanent deletion after **30 days**.

**POST** `/v1/auth/delete-account`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Your account has been scheduled for deletion. It will be permanently deleted after 30 days. You can contact support to restore it within this window.",
    "data": []
}
```

---

### 2.5 Restore Account

Restores a soft-deleted account within the 30-day window. This endpoint is intentionally **outside** `auth:sanctum` because the user has no active tokens.

**POST** `/v1/auth/restore-account`

**Headers:** `Accept: application/json`

**Request Body:**
```json
{
    "email": "aarav.mehta@example.com",
    "password": "password@123"
}
```

**Success (200):**
```json
{
    "status": true,
    "message": "Your account has been restored. Please log in to continue.",
    "data": {
        "user": { ... }
    }
}
```

**Error — Not found:**
```json
{
    "status": false,
    "message": "No account found with this email address.",
    "errors": []
}
```

**Error — Wrong password:**
```json
{
    "status": false,
    "message": "Invalid credentials.",
    "errors": []
}
```

**Error — Already active:**
```json
{
    "status": false,
    "message": "Your account is already active.",
    "errors": []
}
```

**Error — Past 30 days:**
```json
{
    "status": false,
    "message": "The 30-day restoration period has passed. Your account has been permanently deleted.",
    "errors": []
}
```

---

## 3. Profile Management

### 3.1 List Available Profile Types (Public)

**GET** `/profile-types`

**Headers:** `Accept: application/json`

**Success (200):**
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

### 3.2 List My Profiles

**GET** `/v1/profiles`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Profiles retrieved successfully.",
    "data": {
        "profiles": [
            {
                "id": 1,
                "user_id": 1,
                "type": "personal",
                "status": "active",
                "is_active": true,
                "is_default": true,
                "sellerDetails": null,
                "employerDetails": null,
                "musicDetails": null,
                "creatorDetails": null,
                "activeSubscription": { ... }
            }
        ],
        "active_profile": { ... }
    }
}
```

---

### 3.3 Get Active Profile

**GET** `/v1/profiles/active`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Active profile retrieved successfully.",
    "data": {
        "profile": { ... }
    }
}
```

**Error (404):**
```json
{
    "status": false,
    "message": "No active profile found.",
    "errors": []
}
```

---

### 3.4 Get Single Profile

**GET** `/v1/profiles/{id}`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Profile retrieved successfully.",
    "data": {
        "profile": { ... }
    }
}
```

**Error (404):**
```json
{
    "status": false,
    "message": "Profile not found.",
    "errors": []
}
```

---

### 3.5 Create a Profile

**POST** `/v1/profiles`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`  
**Rate Limit:** 10 requests per minute

**Request Body — Employer:**
```json
{
    "type": "employer",
    "company_name": "TechNova Solutions",
    "industry": "Information Technology",
    "company_size": "201-500",
    "company_website": "https://technova.com",
    "company_phone": "9876543210",
    "company_address": "Bandra West, Mumbai"
}
```

**Request Body — Seller (products):**
```json
{
    "type": "seller",
    "seller_type": "products",
    "business_name": "Aarav Electronics",
    "business_description": "Selling premium electronics",
    "business_email": "aarav.biz@example.com",
    "business_phone": "9876543210",
    "business_address": "CP, New Delhi"
}
```

**Request Body — Music:**
```json
{
    "type": "music",
    "artist_name": "Neon Waves",
    "stage_name": "NeonW",
    "genre": "Electronic",
    "label": "Independent",
    "bio": "Electronic music producer from Mumbai."
}
```

**Request Body — Creator:**
```json
{
    "type": "creator",
    "channel_name": "AaravTechReviews",
    "content_category": "Tech Reviews",
    "platform": "YouTube",
    "bio": "Reviewing latest gadgets."
}
```

| Field | Type | Rules |
|-------|------|-------|
| type | string | required, in:employer,seller,music,creator |
| seller_type | string | required_if:type,seller, in:products,services,both |
| company_name | string | required_if:type,employer, max:255 |
| channel_name | string | required_if:type,creator, max:255 |
| business_name | string | nullable, max:255 |
| industry | string | nullable |
| company_size | string | nullable |
| company_website | url | nullable |
| artist_name | string | nullable |
| stage_name | string | nullable |
| genre | string | nullable |
| label | string | nullable |
| bio | string | nullable, max:5000 |

**Success (201) — pending approval:**
```json
{
    "status": true,
    "message": "Profile created successfully. Pending admin approval.",
    "data": {
        "profile": { ... }
    }
}
```

**Error — Duplicate (409):**
```json
{
    "status": false,
    "message": "You already have a employer profile.",
    "errors": []
}
```

**Error — Seller "both" without subscription (422):**
```json
{
    "status": false,
    "message": "A subscription is required to sell both products and services. Please purchase a subscription first.",
    "errors": []
}
```

---

### 3.6 Delete a Profile

**DELETE** `/v1/profiles/{id}`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Profile deleted successfully.",
    "data": []
}
```

**Error — Default profile (422):**
```json
{
    "status": false,
    "message": "The default Personal profile cannot be deleted.",
    "errors": []
}
```

---

### 3.7 Request Profile Switch

**POST** `/v1/profiles/switch`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`  
**Rate Limit:** 5 requests per minute

**Request Body:**
```json
{
    "to_profile_type": "employer",
    "notes": "I want to switch to my employer profile."
}
```

| Field | Type | Rules |
|-------|------|-------|
| to_profile_type | string | required, in:personal,employer,seller,music,creator |
| notes | string | nullable, max:2000 |

**Success (201):**
```json
{
    "status": true,
    "message": "Approval is pending.",
    "data": {
        "switch_request": { ... }
    }
}
```

---

### 3.8 Get My Switch Requests

**GET** `/v1/profile-switch-requests`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

### 3.9 Get Profile Configuration

Returns the authenticated user's complete profile configuration including all profile types, their details, and active subscriptions in a single optimized response.

**GET** `/v1/profiles/config`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Profile configuration retrieved successfully.",
    "data": {
        "user_profile": {
            "user_id": "188f13e4-...",
            "full_name": "Aarav Mehta",
            "username": "aarav_mehta",
            "email": "aarav.mehta@example.com",
            "phone": "9876543210",
            "profile_photo_url": "https://ui-avatars.com/api/?name=...",
            "bio": "Senior Full Stack Developer",
            "gender": "male",
            "language_preference": "en",
            "is_verified": false,
            "followers_count": 0,
            "following_count": 0,
            "posts_count": 0,
            "reputation_score": 0,
            "created_at": "2026-06-05T09:43:05+00:00",
            "last_login_at": "2026-06-05T10:00:00+00:00",
            "current_profile_name": "personal"
        },
        "employer": {
            "is_active": true,
            "company_name": "TechNova Solutions",
            "industry": "Information Technology",
            "company_size": "201-500",
            "company_website": "https://technova.com",
            "company_phone": "9876543210",
            "company_address": "Bandra West, Mumbai",
            "subscription": {
                "is_active": true,
                "plan_name": "Enterprise Plus",
                "plan_slug": "employer-enterprise",
                "price": 999.00,
                "currency": "INR",
                "duration_days": 365,
                "billing_cycle": "yearly",
                "features": ["...", "..."],
                "start_date": "2026-01-01T00:00:00+00:00",
                "expiry_date": "2026-12-31T00:00:00+00:00",
                "next_billing_date": null,
                "auto_renew": true
            }
        },
        "ecommerce": {
            "type": "both",
            "seller_type_label": "Products & Services",
            "product": {
                "enabled": true,
                "total_products": 148,
                "featured_product": {
                    "product_id": "prd_9021",
                    "name": "Wireless Headphones",
                    "price": 8999,
                    "currency": "INR",
                    "stock": 57
                }
            },
            "service": {
                "enabled": true,
                "total_services": 12,
                "active_services": [
                    {"service_id": "srv_201", "name": "Premium Installation", "price": 1499, "currency": "INR"}
                ]
            },
            "subscription": { ... }
        },
        "music_play": {
            "is_active": true,
            "artist_name": "Neon Waves",
            "stage_name": "NeonW",
            "genre": "Electronic",
            "subscription": {
                "is_active": false,
                "plan_name": "Not Subscribed",
                "price": 0,
                ...
            }
        },
        "content_creation": {
            "is_active": true,
            "channel_name": "AaravTechReviews",
            "subscribers_count": 128400,
            "subscription_details": { ... }
        },
        "personal_profile": {
            "is_active": true,
            "subscription": { ... }
        }
    }
}
```

> **Note:** Each non-personal profile section appears **only if** the user has that profile type. A user with only `personal` + `employer` profiles will receive only `user_profile`, `employer`, and `personal_profile`.

---

### 3.10 Get Seller Details

**GET** `/v1/profiles/{id}/seller-details`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

### 3.11 Update Seller Details

**PUT** `/v1/profiles/{id}/seller-details`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "seller_type": "products",
    "business_name": "Aarav Electronics Updated",
    "business_description": "Updated description",
    "business_email": "aarav.biz@example.com",
    "business_phone": "9876543210",
    "business_address": "CP, New Delhi"
}
```

| Field | Type | Rules |
|-------|------|-------|
| seller_type | string | sometimes, in:products,services,both |
| business_name | string | nullable, max:255 |
| business_description | string | nullable, max:2000 |
| business_email | email | nullable |
| business_phone | string | nullable, max:20 |
| business_address | string | nullable, max:500 |

---

### 3.12 Get Employer Details

**GET** `/v1/profiles/{id}/employer-details`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

### 3.13 Get Music Details

**GET** `/v1/profiles/{id}/music-details`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

### 3.14 Get Creator Details

**GET** `/v1/profiles/{id}/creator-details`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

## 4. Admin — Profile Approval

### 4.1 List Pending Switch Requests

**GET** `/v1/admin/profile-switch-requests`

**Headers:** `Accept: application/json`, `Authorization: Bearer {admin_token}`

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| profile_type | string | Filter by to_profile_type |
| per_page | int | Pagination (default 20) |

---

### 4.2 Get Switch Request Details

**GET** `/v1/admin/profile-switch-requests/{id}`

**Headers:** `Accept: application/json`, `Authorization: Bearer {admin_token}`

---

### 4.3 Approve / Reject Switch Request

**POST** `/v1/admin/profile-switch-requests/{id}/approve`

**Headers:** `Accept: application/json`, `Authorization: Bearer {admin_token}`  
**Rate Limit:** 30 requests per minute

**Request Body — Approve:**
```json
{
    "status": "approved",
    "admin_notes": "Verified documents. Profile switch approved."
}
```

**Request Body — Reject:**
```json
{
    "status": "rejected",
    "admin_notes": "Documents not verified. Please re-submit."
}
```

| Field | Type | Rules |
|-------|------|-------|
| status | string | required, in:approved,rejected |
| admin_notes | string | nullable, max:2000 |

**Success (200) — Approved:**
```json
{
    "status": true,
    "message": "Profile switch approved successfully.",
    "data": {
        "switch_request": { ... },
        "new_active_profile": { ... },
        "assigned_subscription": { ... }
    }
}
```

**Error — Already processed (422):**
```json
{
    "status": false,
    "message": "This request has already been approved.",
    "errors": []
}
```

---

## 5. Subscription Management

### 5.1 List Subscription Plans (Public)

**GET** `/subscription-plans`

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| profile_type | string | Filter: `seller`, `creator`, `personal`, `employer`, `music` |

**Example:** `/subscription-plans?profile_type=seller`

**Success (200):**
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
            },
            {
                "id": 2,
                "profile_type": "seller",
                "name": "Pro Seller",
                "slug": "seller-pro",
                "description": "Sell both products AND services.",
                "price": "499.00",
                "currency": "INR",
                "duration_days": 30,
                "features": ["Unlimited products", "Sell both types", "Priority support"],
                "is_active": true,
                "is_default": false,
                "sort_order": 2
            }
        ],
        "filters": {
            "profile_type": "seller"
        }
    }
}
```

---

### 5.2 Get Plan Details (Public)

**GET** `/subscription-plans/{id}`

---

### 5.3 Get Active Subscription for Profile

**GET** `/v1/profiles/{profileId}/subscriptions/active`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

**Success (200):**
```json
{
    "status": true,
    "message": "Active subscription retrieved successfully.",
    "data": {
        "subscription": {
            "id": 1,
            "user_id": 1,
            "profile_id": 1,
            "subscription_plan_id": 1,
            "starts_at": "2026-01-01T00:00:00.000000Z",
            "ends_at": "2026-12-31T00:00:00.000000Z",
            "status": "active",
            "auto_renew": true,
            "next_billing_at": null,
            "plan": { ... }
        }
    }
}
```

**Error (404):**
```json
{
    "status": false,
    "message": "No active subscription found for this profile.",
    "errors": []
}
```

---

### 5.4 Get Subscription History for Profile

**GET** `/v1/profiles/{profileId}/subscriptions/history`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

### 5.5 Purchase a Subscription

**POST** `/v1/profiles/{profileId}/subscriptions`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`  
**Rate Limit:** 10 requests per minute

**Request Body — Free plan:**
```json
{
    "subscription_plan_id": 1,
    "payment_method": "free"
}
```

**Request Body — Paid plan (Razorpay):**
```json
{
    "subscription_plan_id": 2,
    "payment_method": "razorpay",
    "gateway_subscription_id": "sub_E8XJrM7FkYm8Ht"
}
```

| Field | Type | Rules |
|-------|------|-------|
| subscription_plan_id | integer | required, exists:subscription_plans,id |
| payment_method | string | nullable, in:razorpay,free |
| gateway_subscription_id | string | nullable, unique:user_subscriptions |

**Success (201):**
```json
{
    "status": true,
    "message": "Subscription purchased successfully.",
    "data": {
        "subscription": { ... }
    }
}
```

**Error — Already active (422):**
```json
{
    "status": false,
    "message": "This profile already has an active subscription.",
    "errors": []
}
```

**Error — Plan mismatch (422):**
```json
{
    "status": false,
    "message": "The selected plan is not available for seller profiles.",
    "errors": []
}
```

---

### 5.6 Cancel a Subscription

**POST** `/v1/subscriptions/{id}/cancel`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`  
**Rate Limit:** 5 requests per minute

**Request Body:**
```json
{
    "reason": "Switching to a different plan"
}
```

**Success (200):**
```json
{
    "status": true,
    "message": "Subscription cancelled successfully.",
    "data": {
        "subscription": {
            "id": 1,
            "status": "cancelled",
            "cancelled_at": "2026-06-05T12:00:00.000000Z",
            "auto_renew": false,
            ...
        }
    }
}
```

**Error (422):**
```json
{
    "status": false,
    "message": "Only active subscriptions can be cancelled.",
    "errors": []
}
```

---

## 6. Profile Configuration

### 6.1 Profile Config (Aggregated View)

Returns all profile sections and their subscriptions in one call. See section [3.9](#39-get-profile-configuration) for details.

---

## 7. Ecommerce — Products

All product endpoints require `auth:sanctum` + `is_seller` check.

### 7.1 Toggle Seller Status

**POST** `/v1/seller`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

### 7.2 Seller Dashboard Stats

**GET** `/v1/seller/dashboard/stats`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

### 7.3 List Seller Products

**GET** `/v1/seller/products`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

### 7.4 Get Single Product

**GET** `/v1/seller/products/{productIdentifier}`

### 7.5 Get Seller Products (by seller ID)

**GET** `/v1/seller/{sellerId}/products`

### 7.6 Create Product

**POST** `/v1/seller/products`

**Headers:** `Authorization: Bearer {token}` (multipart/form-data)

| Field | Type | Rules |
|-------|------|-------|
| title | string | required, max:255, unique:user_products |
| description | string | nullable |
| price | numeric | required, min:0 |
| discount_price | numeric | nullable, min:0, lt:price |
| stock | integer | nullable, min:0 |
| cover_image | file | nullable, jpeg/jpg/png/webp, max:5MB |
| images[] | file[] | nullable, jpeg/jpg/png/webp, max:5MB each |

### 7.7 Update Product

**POST/PUT/PATCH** `/v1/seller/products/{product}`

**Headers:** `Authorization: Bearer {token}` (multipart/form-data)

### 7.8 Delete Product

**DELETE** `/v1/seller/products/{product}`

**Headers:** `Accept: application/json`, `Authorization: Bearer {token}`

---

## 8. Ecommerce — Services

### 8.1 List Services

**GET** `/v1/services`

### 8.2 Get Seller Services

**GET** `/v1/services/{sellerId}/services`

### 8.3 Create Service

**POST** `/v1/services`

**Headers:** `Authorization: Bearer {token}` (multipart/form-data)

| Field | Type | Rules |
|-------|------|-------|
| title | string | required, max:255, unique:user_services |
| description | string | nullable |
| price | numeric | required, min:0 |
| discount_price | numeric | nullable, min:0, lt:price |
| cover_image | file | nullable, jpeg/jpg/png/webp, max:5MB |
| images[] | file[] | nullable |

### 8.4 Update Service

**POST** `/v1/services/{service}`

### 8.5 Delete Service

**DELETE** `/v1/services/{service}`

---

## 9. Webhook

### 9.1 Razorpay Webhook

**POST** `/webhooks/razorpay`

**Headers:**
| Header | Value |
|--------|-------|
| Content-Type | application/json |
| X-Razorpay-Signature | `{sha256_hmac_signature}` |

**Rate Limit:** 30 requests per minute.  
**CSRF:** Disabled for this endpoint.

**Events handled:**
| Event | Action |
|-------|--------|
| `subscription.charged` | Status→active, generate invoice, refresh next_billing_at |
| `subscription.cancelled` | Status→cancelled, auto_renew→false |
| `subscription.completed` | Status→expired |
| `payment.failed` | Status→past_due |

**Success (200):**
```json
{ "status": "success" }
```

**Error — Bad signature (400):**
```json
{ "status": "error", "message": "Invalid signature" }
```

**Error — Subscription not found (404):**
```json
{ "status": "error", "message": "Subscription not found" }
```

---

## Supplementary APIs

### Get Interests (Public)

**GET** `/interests`

### Get Banners

**GET** `/v1/banners`

### Clear Cache (Dev Only)

**GET** `/v1/clear-cache`

### Forgot Password — Verify OTP

**POST** `/forgot-password/verify`

### Forgot Password — Reset

**POST** `/forgot-password/reset`

---

## Caching

| Cache Key | TTL | Busted On |
|-----------|-----|-----------|
| `profile_config:{user_id}` | 300s | Profile created/updated/deleted, Subscription purchased/cancelled |

---

## Postman Collection

A fully configured `postman_collection.json` (v2.1) is included in the project root. Import it directly into Postman — all endpoints have pre-configured headers, URLs, and example request bodies.
