# Subscription Plans & Dynamic Features Integration Guide

This guide is designed for Flutter frontend developers to integrate the **Dynamic Subscription Plans** and **Dynamic Features** system. 

The system operates dynamically: plans, prices, durations, and feature limits are fetched from the API and should be rendered dynamically in the UI. However, features have different implementation statuses on the backend. This guide highlights which features are fully functional (**Active / Implemented**) and which ones are currently **Static Placeholders (Unavailable)** to help you structure the UI, badges, and user gates properly.

---

## 1. Feature Implementation Status Matrix

When rendering the user's dashboard, settings, or plan comparison screens, check this list. Features marked as **Unavailable** should either be hidden, labeled as "Coming Soon", or simulated with static placeholder values in the app since they are not backed by active logic on the server.

### Core User Features (Personal Profile)

| Feature Name | Slug / Key | Description | Status |
| :--- | :--- | :--- | :--- |
| **Visibility Multiplier** | `visibility_multiplier` | Boosts content reach (e.g., 1.2x, 4.0x) | **🟢 Active / Implemented** |
| **Job Priority** | `job_priority` | Weight ranking in job applications | **🟢 Active / Implemented** |
| **DM Recruiters** | `dm_recruiters_msme` | Direct messaging access to recruiter/business accounts | **🟢 Active / Implemented** |
| **Sell Services** | `sell_services` | Ability to list and sell paid services | **🟢 Active / Implemented** |
| **Ads Frequency** | `ads_frequency` | Frequency of ads shown to the user | **🔴 Static Placeholder (Unavailable)** |
| **Boost Credits** | `boost_credits` | Tokens to manually promote posts | **🔴 Static Placeholder (Unavailable)** |
| **AI Tools** | `ai_tools` | AI-based suggestions for profile & content growth | **🔴 Static Placeholder (Unavailable)** |
| **Tipping (Collect)** | `tipping_i_shoutpay` | Collect tips directly from other users | **🔴 Static Placeholder (Unavailable)** |
| **Creator Monetization** | `creator_monetization` | General monetization tools (revenue, tips, store) | **🔴 Static Placeholder (Unavailable)** |
| **Affiliate Earnings** | `affiliate_earnings` | Earn commission by promoting items | **🔴 Static Placeholder (Unavailable)** |
| **Job Referral Earnings** | `job_referral_earnings` | Earn by referring candidates or jobs | **🔴 Static Placeholder (Unavailable)** |
| **Ad Revenue Share** | `ad_revenue_share` | Receive share of ad revenue shown on posts | **🔴 Static Placeholder (Unavailable)** |
| **Platform Fee** | `platform_fee` | Platform commission percentage on earnings | **🔴 Static Placeholder (Unavailable)** |
| **Transaction Charges** | `transaction_charges` | Payment processing charges | **🔴 Static Placeholder (Unavailable)** |
| **Withdrawal System** | `withdrawal_system` | Transfer earnings to bank | **🔴 Static Placeholder (Unavailable)** |
| **Feed Priority** | `feed_priority` | Boost position of content in user feeds | **🔴 Static Placeholder (Unavailable)** |
| **Content Reach Cap** | `content_reach_cap` | Cap limit on maximum reach per post | **🔴 Static Placeholder (Unavailable)** |
| **Discovery Access** | `discovery_access` | Explore page and trending listing algorithm | **🔴 Static Placeholder (Unavailable)** |
| **Profile Intent Tag** | `profile_intent_tag` | UI tags showcasing user intent (job, seller, etc.) | **🔴 Static Placeholder (Unavailable)** |
| **Private Groups Access** | `private_groups_access` | Ability to join private niche communities | **🔴 Static Placeholder (Unavailable)** |
| **Events Access** | `events_access` | Entrance to online/offline group events | **🔴 Static Placeholder (Unavailable)** |

---

### Creator-Specific Features (Creator Profile)

| Feature Name | Slug / Key | Description | Status |
| :--- | :--- | :--- | :--- |
| **Visibility Boost (Base)** | `visibility_boost_creator` | Multiplies content reach (e.g., 1.8x, 3.0x) | **🟢 Active / Implemented** |
| **Sell Services / Gigs** | `sell_services_gigs` | List and execute custom gigs or services | **🟢 Active / Implemented** |
| **Creator Badge** | `creator_badge` | Badge representing identity as verified creator | **🔴 Static Placeholder (Unavailable)** |
| **Content Reach Priority** | `content_reach_priority` | Priority index in reach algorithm | **🔴 Static Placeholder (Unavailable)** |
| **Monetization Access** | `monetization_access` | Open payout accounts and earn on platform | **🔴 Static Placeholder (Unavailable)** |
| **Tipping (i-ShoutPay™)** | `tipping_i_shoutpay_creator` | Fans tipping the creator directly | **🔴 Static Placeholder (Unavailable)** |
| **Boost Credits Creator** | `boost_credits_creator` | Creator-specific post promotion tokens | **🔴 Static Placeholder (Unavailable)** |
| **Creator Analytics** | `creator_analytics` | Deep metrics dashboard on performance | **🔴 Static Placeholder (Unavailable)** |
| **Local Discovery Listing** | `local_discovery_listing` | Discoverable in localized geography search | **🔴 Static Placeholder (Unavailable)** |
| **Creator Storefront** | `creator_storefront` | Digital storefront layout on profile page | **🔴 Static Placeholder (Unavailable)** |
| **UPI Payments** | `upi_payments` | Direct UPI payment integration | **🔴 Static Placeholder (Unavailable)** |
| **Affiliate Earnings Creator**| `affiliate_earnings_creator` | Commissions on creator storefront products | **🔴 Static Placeholder (Unavailable)** |
| **Ad Revenue Share Creator** | `ad_revenue_share_creator` | Ad revenue split on creator content page | **🔴 Static Placeholder (Unavailable)** |
| **AI Content Assistant** | `ai_content_assistant` | AI helper for caption and content writing | **🔴 Static Placeholder (Unavailable)** |
| **Profile Customization** | `profile_customization` | Special profile layouts, backgrounds, colors | **🔴 Static Placeholder (Unavailable)** |
| **Creator Score (Trust Rank)**| `creator_score_trust_rank`| Rating based on activity, safety, and reviews | **🔴 Static Placeholder (Unavailable)** |
| **Collab Access** | `collab_access` | Find and collaborate with other creators/brands | **🔴 Static Placeholder (Unavailable)** |
| **Brand Deal Visibility** | `brand_deal_visibility` | Highlighted to brands for sponsorships | **🔴 Static Placeholder (Unavailable)** |

---

## 2. Dynamic Subscription Plans & Values

Your app must render these values dynamically. Below are the default seed plans configured in the backend database. 

### A. Personal Profile Tiers
* **Open** (Free, Lifetime)
  * `visibility_multiplier`: `1.0x` **[🟢 Active]**
  * `job_priority`: `0` **[🟢 Active]**
  * `dm_recruiters_msme`: `No` **[🟢 Active]**
  * `sell_services`: `No` **[🟢 Active]**
  * *Other static/unavailable features*: `ads_frequency: High`, `boost_credits: 0`, `ai_tools: No`, `tipping_i_shoutpay: No`, `affiliate_earnings: No`, `ad_revenue_share: No`, `platform_fee: 15%`.
* **Plus** (₹119 / 30 days)
  * `visibility_multiplier`: `1.2x` **[🟢 Active]**
  * `job_priority`: `0` **[🟢 Active]**
  * `dm_recruiters_msme`: `No` **[🟢 Active]**
  * `sell_services`: `No` **[🟢 Active]**
  * *Other static/unavailable features*: `ads_frequency: Medium`, `boost_credits: 0`, `ai_tools: No`, `tipping_i_shoutpay: No`, `affiliate_earnings: No`, `ad_revenue_share: No`, `platform_fee: 15%`.
* **Growth** (₹149 / 30 days)
  * `visibility_multiplier`: `1.4x` **[🟢 Active]**
  * `job_priority`: `1` **[🟢 Active]**
  * `dm_recruiters_msme`: `No` **[🟢 Active]**
  * `sell_services`: `No` **[🟢 Active]**
  * *Other static/unavailable features*: `ads_frequency: Medium`, `boost_credits: 0`, `ai_tools: No`, `tipping_i_shoutpay: No`, `affiliate_earnings: No`, `ad_revenue_share: No`, `platform_fee: 15%`.
* **Pro+** (₹299 / 30 days)
  * `visibility_multiplier`: `1.8x` **[🟢 Active]**
  * `job_priority`: `3` **[🟢 Active]**
  * `dm_recruiters_msme`: `Yes` **[🟢 Active]**
  * `sell_services`: `Yes` **[🟢 Active]**
  * *Other static/unavailable features*: `ads_frequency: Low`, `boost_credits: 1`, `ai_tools: No`, `tipping_i_shoutpay: Yes`, `affiliate_earnings: Yes`, `ad_revenue_share: No`, `platform_fee: 15%`.
* **Prime** (₹549 / 30 days)
  * `visibility_multiplier`: `2.5x` **[🟢 Active]**
  * `job_priority`: `4` **[🟢 Active]**
  * `dm_recruiters_msme`: `Yes` **[🟢 Active]**
  * `sell_services`: `Yes` **[🟢 Active]**
  * *Other static/unavailable features*: `ads_frequency: Low`, `boost_credits: 3`, `ai_tools: Yes`, `tipping_i_shoutpay: Yes`, `affiliate_earnings: Yes`, `ad_revenue_share: Yes`, `platform_fee: 10%`.
* **Infinity** (₹999 / 30 days)
  * `visibility_multiplier`: `4.0x` **[🟢 Active]**
  * `job_priority`: `5` **[🟢 Active]**
  * `dm_recruiters_msme`: `Yes` **[🟢 Active]**
  * `sell_services`: `Yes` **[🟢 Active]**
  * *Other static/unavailable features*: `ads_frequency: Major Low`, `boost_credits: 5`, `ai_tools: Yes`, `tipping_i_shoutpay: Yes`, `affiliate_earnings: Yes`, `ad_revenue_share: Yes`, `platform_fee: 5%`.

---

### B. Creator Profile Tiers
* **Free Creator** (Free, Lifetime)
  * `visibility_boost_creator`: `1.1x` **[🟢 Active]**
  * `sell_services_gigs`: `No` **[🟢 Active]**
  * *Other static/unavailable features*: `creator_badge: No`, `content_reach_priority: Low`, `monetization_access: No`, `tipping_i_shoutpay_creator: No`, `boost_credits_creator: 0`, `creator_analytics: Low basic`, `local_discovery_listing: No`, `creator_storefront: No`, `upi_payments: No`, `affiliate_earnings_creator: No`, `ad_revenue_share_creator: No`, `ai_content_assistant: No`, `profile_customization: No`, `creator_score_trust_rank: No`, `collab_access: No`, `brand_deal_visibility: No`, `support_level: Basic`.
* **Creator Start** (₹299 / 30 days)
  * `visibility_boost_creator`: `1.8x` **[🟢 Active]**
  * `sell_services_gigs`: `Limited` **[🟢 Active]**
  * *Other static/unavailable features*: `creator_badge: Yes`, `content_reach_priority: Medium`, `monetization_access: Limited`, `tipping_i_shoutpay_creator: Yes`, `boost_credits_creator: 3/month`, `creator_analytics: Basic`, `local_discovery_listing: Yes`, `creator_storefront: No`, `upi_payments: No`, `affiliate_earnings_creator: No`, `ad_revenue_share_creator: No`, `ai_content_assistant: No`, `profile_customization: Limited`, `creator_score_trust_rank: No`, `collab_access: Basic`, `brand_deal_visibility: Limited`, `support_level: Standard`.
* **Creator Pro** (₹699 / 30 days)
  * `visibility_boost_creator`: `3.0x` **[🟢 Active]**
  * `sell_services_gigs`: `Full` **[🟢 Active]**
  * *Other static/unavailable features*: `creator_badge: Yes (Highlighted)`, `content_reach_priority: High`, `monetization_access: Full`, `tipping_i_shoutpay_creator: Yes`, `boost_credits_creator: 15/month`, `creator_analytics: Advanced`, `local_discovery_listing: Priority`, `creator_storefront: Yes`, `upi_payments: Yes`, `affiliate_earnings_creator: Yes`, `ad_revenue_share_creator: Yes`, `ai_content_assistant: Yes`, `profile_customization: Full`, `creator_score_trust_rank: Yes`, `collab_access: Priority`, `brand_deal_visibility: High`, `support_level: Priority`.

---

### C. Seller Profile Tiers (Marketplace / E-Commerce)
* **Basic Seller** (Free, Lifetime)
  * `features`: `List up to 10 products`, `Basic analytics`, `Standard support`
* **Pro Seller** (₹499 / 30 days)
  * `features`: `Unlimited products`, `Unlimited services`, `Sell both types`, `Advanced analytics`, `Priority support`, `Featured listings`
* **Enterprise Seller** (₹1499 / 90 days)
  * `features`: `Everything in Pro`, `Bulk product upload`, `Dedicated account manager`, `API access`, `Custom storefront`, `Advanced reporting`

---

### D. Employer Profile Tiers
* **Basic Employer** (Free, Lifetime)
  * `features`: `Post up to 3 jobs`, `Basic candidate search`, `Standard support`
* **Pro Employer** (₹999 / 30 days)
  * `features`: `Unlimited job posts`, `Advanced candidate filtering`, `Priority applicant support`, `Resume downloads`, `Premium badge`

---

### E. Music Profile Tiers
* **Basic Music** (Free, Lifetime)
  * `features`: `Create playlists`, `Standard streaming quality`, `List up to 5 tracks`
* **Premium Music** (₹199 / 30 days)
  * `features`: `Ad-free listening`, `Ultra-HQ audio streaming`, `Unlimited track uploads`, `Offline playback`, `Exclusive artist badge`

---

## 3. Flutter Integration API Reference

### 3.1 Fetch Subscription Plans
Get available plans. Filter by `profile_type` query parameter (`personal`, `creator`, `seller`, `employer`, `music`) to show target options.

* **Endpoint**: `GET /api/subscription-plans`
* **Query Params**: `profile_type=personal`
* **Response Snippet**:
```json
{
    "status": true,
    "message": "Subscription plans retrieved successfully.",
    "data": {
        "plans": [
            {
                "id": 1,
                "profile_type": "personal",
                "name": "Open",
                "slug": "personal-open",
                "description": "Basic personal profile with essential features.",
                "price": "0.00",
                "currency": "INR",
                "duration_days": 36500,
                "is_active": true,
                "is_default": true,
                "sort_order": 1,
                "features": [
                    {
                        "id": 1,
                        "name": "Visibility Multiplier",
                        "slug": "visibility_multiplier",
                        "description": "Boosts how many people see your content",
                        "is_implemented": true,
                        "pivot": {
                            "subscription_plan_id": 1,
                            "feature_id": 1,
                            "limit_value": "1.0x"
                        }
                    }
                ]
            }
        ]
    }
}
```
> **Tip for Flutter developers:** Inside each plan's `features` list, read `is_implemented` (boolean) to determine whether to render it as an active benefit or as a static preview/coming-soon item. Read `pivot.limit_value` to render the exact metric or string (e.g. `1.0x`, `Yes`, `Medium`).

---

### 3.2 Initiate Subscription Payment
Initiate the subscription payment transaction for the target profile.

* **Endpoint**: `POST /api/v1/profiles/{profileId}/subscriptions/initiate`
* **Headers**: `Authorization: Bearer <token>`
* **Payload**:
```json
{
    "subscription_plan_id": 4
}
```
* **Response (Paid Plan)**:
```json
{
    "status": true,
    "message": "Subscription initiated successfully.",
    "data": {
        "requires_payment": true,
        "gateway": "phonepe",
        "gateway_subscription_id": "sub_12345abcdef",
        "razorpay_key": "rzp_test_...",
        "redirect_url": "https://merch.phonepe.com/pay/...",
        "plan": {
            "id": 4,
            "name": "Pro+",
            "price": 299,
            "currency": "INR"
        }
    }
}
```

* **SDK/WebView Handling**:
  - If `requires_payment` is `false`, the subscription (usually Free plan) is activated directly.
  - If `gateway` is `phonepe`, open the provided `redirect_url` in a Webview/Browser.
  - If `gateway` is `razorpay`, trigger the native Razorpay SDK checkout passing the `gateway_subscription_id` and `razorpay_key`.

---

### 3.3 Get Active Subscription
Check if a profile currently has an active subscription.

* **Endpoint**: `GET /api/v1/profiles/{profileId}/subscriptions/active`
* **Response**:
```json
{
    "status": true,
    "message": "Active subscription retrieved successfully.",
    "data": {
        "subscription": {
            "id": 18,
            "user_id": 1,
            "profile_id": 12,
            "subscription_plan_id": 4,
            "starts_at": "2026-06-25T09:00:00.000000Z",
            "ends_at": "2026-07-25T09:00:00.000000Z",
            "status": "active",
            "plan": {
                "id": 4,
                "name": "Pro+"
            }
        }
    }
}
```
