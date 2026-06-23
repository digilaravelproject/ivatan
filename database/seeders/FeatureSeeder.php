<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Features
        $features = [
            // Core User Features
            ['name' => 'Visibility Multiplier', 'slug' => 'visibility_multiplier', 'description' => 'Boosts how many people see your content', 'is_implemented' => true],
            ['name' => 'Ads Frequency', 'slug' => 'ads_frequency', 'description' => 'Number of ads shown to user', 'is_implemented' => false],
            ['name' => 'Job Priority', 'slug' => 'job_priority', 'description' => 'Ranking weight in job applications', 'is_implemented' => true],
            ['name' => 'DM Recruiters', 'slug' => 'dm_recruiters_msme', 'description' => 'Direct messaging access to hiring/business accounts', 'is_implemented' => true],
            ['name' => 'Boost Credits', 'slug' => 'boost_credits', 'description' => 'Manual promotion tokens for posts', 'is_implemented' => false],
            ['name' => 'AI Tools', 'slug' => 'ai_tools', 'description' => 'AI-based suggestions (profile, content, growth tips)', 'is_implemented' => false],
            ['name' => 'Leaderboard Access', 'slug' => 'leaderboard_access', 'description' => 'Ranking system (top creators/users in city/category)', 'is_implemented' => false],
            ['name' => 'Custom URL', 'slug' => 'custom_url', 'description' => 'Personalized profile link', 'is_implemented' => false],
            ['name' => 'Support Level', 'slug' => 'support_level', 'description' => 'Customer support priority', 'is_implemented' => false],
            ['name' => 'Tipping (Collect)', 'slug' => 'tipping_i_shoutpay', 'description' => 'Users collect tips directly from others', 'is_implemented' => false],
            ['name' => 'Sell Services', 'slug' => 'sell_services', 'description' => 'Offer paid services (design, promo, gigs)', 'is_implemented' => true],
            ['name' => 'Creator Monetization', 'slug' => 'creator_monetization', 'description' => 'Earn via content (tips, promotions, store)', 'is_implemented' => false],
            ['name' => 'Affiliate Earnings', 'slug' => 'affiliate_earnings', 'description' => 'Earn commission by promoting products/services', 'is_implemented' => false],
            ['name' => 'Job Referral Earnings', 'slug' => 'job_referral_earnings', 'description' => 'Earn by referring candidates/jobs', 'is_implemented' => false],
            ['name' => 'Ad Revenue Share', 'slug' => 'ad_revenue_share', 'description' => 'Share of ads shown on your content', 'is_implemented' => false],
            ['name' => 'Platform Fee', 'slug' => 'platform_fee', 'description' => 'Commission taken by platform on earnings', 'is_implemented' => false],
            ['name' => 'Transaction Charges', 'slug' => 'transaction_charges', 'description' => 'Payment processing fee (UPI/gateway)', 'is_implemented' => false],
            ['name' => 'Withdrawal System', 'slug' => 'withdrawal_system', 'description' => 'Transfer earnings to bank', 'is_implemented' => false],
            ['name' => 'Feed Priority', 'slug' => 'feed_priority', 'description' => 'Position of content in feed', 'is_implemented' => false],
            ['name' => 'Content Reach Cap', 'slug' => 'content_reach_cap', 'description' => 'Maximum audience per post', 'is_implemented' => false],
            ['name' => 'Discovery Access', 'slug' => 'discovery_access', 'description' => 'Visibility in explore/trending sections', 'is_implemented' => false],
            ['name' => 'Profile Intent Tag', 'slug' => 'profile_intent_tag', 'description' => 'Defines user purpose (job, creator, seller)', 'is_implemented' => false],
            ['name' => 'Private Groups Access', 'slug' => 'private_groups_access', 'description' => 'Join niche communities', 'is_implemented' => false],
            ['name' => 'Events Access', 'slug' => 'events_access', 'description' => 'Access to online/offline sessions', 'is_implemented' => false],

            // Creator Specific Features
            ['name' => 'Creator Badge', 'slug' => 'creator_badge', 'description' => 'Identity as creator', 'is_implemented' => false],
            ['name' => 'Visibility Boost (Base)', 'slug' => 'visibility_boost_creator', 'description' => 'Multiplies reach vs normal users', 'is_implemented' => true],
            ['name' => 'Content Reach Priority', 'slug' => 'content_reach_priority', 'description' => 'Content Reach Priority', 'is_implemented' => false],
            ['name' => 'Monetization Access', 'slug' => 'monetization_access', 'description' => 'Ability to earn via platform', 'is_implemented' => false],
            ['name' => 'Tipping (i-ShoutPay™)', 'slug' => 'tipping_i_shoutpay_creator', 'description' => 'Fans pay directly', 'is_implemented' => false],
            ['name' => 'Boost Credits Creator', 'slug' => 'boost_credits_creator', 'description' => 'Manual promotion tokens', 'is_implemented' => false],
            ['name' => 'Creator Analytics', 'slug' => 'creator_analytics', 'description' => 'Performance data', 'is_implemented' => false],
            ['name' => 'Local Discovery Listing', 'slug' => 'local_discovery_listing', 'description' => 'Appears in local search', 'is_implemented' => false],
            ['name' => 'Creator Storefront', 'slug' => 'creator_storefront', 'description' => 'Mini business profile', 'is_implemented' => false],
            ['name' => 'Sell Services / Gigs', 'slug' => 'sell_services_gigs', 'description' => 'Offer paid work', 'is_implemented' => true],
            ['name' => 'UPI Payments', 'slug' => 'upi_payments', 'description' => 'Direct bank transactions', 'is_implemented' => false],
            ['name' => 'Affiliate Earnings Creator', 'slug' => 'affiliate_earnings_creator', 'description' => 'Earn via promotions', 'is_implemented' => false],
            ['name' => 'Ad Revenue Share Creator', 'slug' => 'ad_revenue_share_creator', 'description' => 'Earn from ads on content', 'is_implemented' => false],
            ['name' => 'AI Content Assistant', 'slug' => 'ai_content_assistant', 'description' => 'Suggests content ideas', 'is_implemented' => false],
            ['name' => 'Profile Customization', 'slug' => 'profile_customization', 'description' => 'Profile Customization', 'is_implemented' => false],
            ['name' => 'Creator Score (Trust Rank)', 'slug' => 'creator_score_trust_rank', 'description' => 'Ranking system', 'is_implemented' => false],
            ['name' => 'Collab Access', 'slug' => 'collab_access', 'description' => 'Brand/creator connections', 'is_implemented' => false],
            ['name' => 'Brand Deal Visibility', 'slug' => 'brand_deal_visibility', 'description' => 'Exposure to businesses', 'is_implemented' => false],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(['slug' => $feature['slug']], $feature);
        }

        // 2. Seed Subscription Plans matching the Roadmap
        $userPlans = [
            [
                'profile_type' => 'personal',
                'name' => 'Open',
                'slug' => 'personal-open',
                'description' => 'Basic personal profile with essential features.',
                'price' => 0.00,
                'currency' => 'INR',
                'duration_days' => 36500,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'feature_values' => [
                    'visibility_multiplier' => '1.0x',
                    'ads_frequency' => 'High',
                    'job_priority' => '0',
                    'dm_recruiters_msme' => 'No',
                    'boost_credits' => '0',
                    'ai_tools' => 'No',
                    'tipping_i_shoutpay' => 'No',
                    'sell_services' => 'No',
                    'affiliate_earnings' => 'No',
                    'ad_revenue_share' => 'No',
                    'platform_fee' => '15%',
                ]
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Plus',
                'slug' => 'personal-plus',
                'description' => 'Plus plan offering improved visibility.',
                'price' => 119.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'feature_values' => [
                    'visibility_multiplier' => '1.2x',
                    'ads_frequency' => 'Medium',
                    'job_priority' => '0',
                    'dm_recruiters_msme' => 'No',
                    'boost_credits' => '0',
                    'ai_tools' => 'No',
                    'tipping_i_shoutpay' => 'No',
                    'sell_services' => 'No',
                    'affiliate_earnings' => 'No',
                    'ad_revenue_share' => 'No',
                    'platform_fee' => '15%',
                ]
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Growth',
                'slug' => 'personal-growth',
                'description' => 'Growth plan with visibility and priority applicant privileges.',
                'price' => 149.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'feature_values' => [
                    'visibility_multiplier' => '1.4x',
                    'ads_frequency' => 'Medium',
                    'job_priority' => '1',
                    'dm_recruiters_msme' => 'No',
                    'boost_credits' => '0',
                    'ai_tools' => 'No',
                    'tipping_i_shoutpay' => 'No',
                    'sell_services' => 'No',
                    'affiliate_earnings' => 'No',
                    'ad_revenue_share' => 'No',
                    'platform_fee' => '15%',
                ]
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Pro+',
                'slug' => 'personal-pro-plus',
                'description' => 'Pro+ plan for serious networking and monetization.',
                'price' => 299.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
                'feature_values' => [
                    'visibility_multiplier' => '1.8x',
                    'ads_frequency' => 'Low',
                    'job_priority' => '3',
                    'dm_recruiters_msme' => 'Yes',
                    'boost_credits' => '1',
                    'ai_tools' => 'No',
                    'tipping_i_shoutpay' => 'Yes',
                    'sell_services' => 'Yes',
                    'affiliate_earnings' => 'Yes',
                    'ad_revenue_share' => 'No',
                    'platform_fee' => '15%',
                ]
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Prime',
                'slug' => 'personal-prime',
                'description' => 'Prime features with maximum benefits.',
                'price' => 549.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
                'feature_values' => [
                    'visibility_multiplier' => '2.5x',
                    'ads_frequency' => 'Low',
                    'job_priority' => '4',
                    'dm_recruiters_msme' => 'Yes',
                    'boost_credits' => '3',
                    'ai_tools' => 'Yes',
                    'tipping_i_shoutpay' => 'Yes',
                    'sell_services' => 'Yes',
                    'affiliate_earnings' => 'Yes',
                    'ad_revenue_share' => 'Yes',
                    'platform_fee' => '10%',
                ]
            ],
            [
                'profile_type' => 'personal',
                'name' => 'Infinity',
                'slug' => 'personal-infinity',
                'description' => 'Infinity tier with ultimate visibility and lower platform fees.',
                'price' => 999.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 6,
                'feature_values' => [
                    'visibility_multiplier' => '4.0x',
                    'ads_frequency' => 'Major Low',
                    'job_priority' => '5',
                    'dm_recruiters_msme' => 'Yes',
                    'boost_credits' => '5',
                    'ai_tools' => 'Yes',
                    'tipping_i_shoutpay' => 'Yes',
                    'sell_services' => 'Yes',
                    'affiliate_earnings' => 'Yes',
                    'ad_revenue_share' => 'Yes',
                    'platform_fee' => '5%',
                ]
            ],
        ];

        $creatorPlans = [
            [
                'profile_type' => 'creator',
                'name' => 'Free Creator',
                'slug' => 'creator-free',
                'description' => 'Free tier to test creator tools.',
                'price' => 0.00,
                'currency' => 'INR',
                'duration_days' => 36500,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'feature_values' => [
                    'creator_badge' => 'No',
                    'visibility_boost_creator' => '1.1x',
                    'content_reach_priority' => 'Low',
                    'monetization_access' => 'No',
                    'tipping_i_shoutpay_creator' => 'No',
                    'boost_credits_creator' => '0',
                    'creator_analytics' => 'Low basic',
                    'local_discovery_listing' => 'No',
                    'creator_storefront' => 'No',
                    'sell_services_gigs' => 'No',
                    'upi_payments' => 'No',
                    'affiliate_earnings_creator' => 'No',
                    'ad_revenue_share_creator' => 'No',
                    'ai_content_assistant' => 'No',
                    'profile_customization' => 'No',
                    'creator_score_trust_rank' => 'No',
                    'collab_access' => 'No',
                    'brand_deal_visibility' => 'No',
                    'support_level' => 'Basic',
                ]
            ],
            [
                'profile_type' => 'creator',
                'name' => 'Creator Start',
                'slug' => 'creator-start',
                'description' => 'Grow your presence and start earning.',
                'price' => 299.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'feature_values' => [
                    'creator_badge' => 'Yes',
                    'visibility_boost_creator' => '1.8x',
                    'content_reach_priority' => 'Medium',
                    'monetization_access' => 'Limited',
                    'tipping_i_shoutpay_creator' => 'Yes',
                    'boost_credits_creator' => '3/month',
                    'creator_analytics' => 'Basic',
                    'local_discovery_listing' => 'Yes',
                    'creator_storefront' => 'No',
                    'sell_services_gigs' => 'Limited',
                    'upi_payments' => 'No',
                    'affiliate_earnings_creator' => 'No',
                    'ad_revenue_share_creator' => 'No',
                    'ai_content_assistant' => 'No',
                    'profile_customization' => 'Limited',
                    'creator_score_trust_rank' => 'No',
                    'collab_access' => 'Basic',
                    'brand_deal_visibility' => 'Limited',
                    'support_level' => 'Standard',
                ]
            ],
            [
                'profile_type' => 'creator',
                'name' => 'Creator Pro',
                'slug' => 'creator-pro-v2',
                'description' => 'Full business mode for professional creators.',
                'price' => 699.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'feature_values' => [
                    'creator_badge' => 'Yes (Highlighted)',
                    'visibility_boost_creator' => '3.0x',
                    'content_reach_priority' => 'High',
                    'monetization_access' => 'Full',
                    'tipping_i_shoutpay_creator' => 'Yes',
                    'boost_credits_creator' => '15/month',
                    'creator_analytics' => 'Advanced',
                    'local_discovery_listing' => 'Priority',
                    'creator_storefront' => 'Yes',
                    'sell_services_gigs' => 'Full',
                    'upi_payments' => 'Yes',
                    'affiliate_earnings_creator' => 'Yes',
                    'ad_revenue_share_creator' => 'Yes',
                    'ai_content_assistant' => 'Yes',
                    'profile_customization' => 'Full',
                    'creator_score_trust_rank' => 'Yes',
                    'collab_access' => 'Priority',
                    'brand_deal_visibility' => 'High',
                    'support_level' => 'Priority',
                ]
            ]
        ];

        // Combine all plans
        $allPlans = array_merge($userPlans, $creatorPlans);

        foreach ($allPlans as $planData) {
            $featureValues = $planData['feature_values'];
            unset($planData['feature_values']);

            // Set features description text for the legacy json cast
            $planData['features'] = array_keys($featureValues);

            // Create/Update the plan
            $plan = SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            // Sync pivot relationship
            $syncData = [];
            foreach ($featureValues as $slug => $value) {
                $feature = Feature::where('slug', $slug)->first();
                if ($feature) {
                    $syncData[$feature->id] = ['limit_value' => $value];
                }
            }

            $plan->features()->sync($syncData);
        }
    }
}
