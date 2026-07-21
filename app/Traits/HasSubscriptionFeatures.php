<?php

namespace App\Traits;

use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\Feature;
use Illuminate\Support\Facades\Cache;

/**
 * @property \App\Models\Profile|null $activeProfile
 * @property \App\Models\Profile|null $defaultProfile
 * @method \Illuminate\Database\Eloquent\Relations\HasMany profiles()
 */
trait HasSubscriptionFeatures
{
    /**
     * Get the dynamic limit value for a specific feature slug.
     *
     * @param string $featureSlug The unique slug of the feature.
     * @param string|null $profileType Optional profile type filter (e.g. personal, seller, creator).
     * @return mixed
     */
    public function getFeatureLimit(string $featureSlug, ?string $profileType = null)
    {
        // 1. Determine which profile to check
        $profile = null;
        if ($profileType) {
            $profile = $this->profiles()->where('type', $profileType)->where('status', 'active')->first();
        } else {
            // Eager load activeProfile if loaded, else query database
            $profile = $this->activeProfile ?: $this->defaultProfile ?: $this->profiles()->where('status', 'active')->first();
        }

        if ($profile) {
            // Get active subscription for this profile
            $subscription = $profile->activeSubscription;
            if ($subscription && $subscription->isActive()) {
                $plan = $subscription->plan;
                if ($plan) {
                    // Try to get the feature from the plan's pivoted features list
                    // Pivot attributes can be retrieved from the `plan_features` intermediate table
                    $feature = $plan->features()->where('slug', $featureSlug)->first();
                    if ($feature) {
                        return $feature->pivot->limit_value;
                    }
                }
            }
        }

        // Fallback default value if no active subscription or feature exists
        return $this->getDefaultFeatureLimit($featureSlug);
    }

    /**
     * Get default values for features based on the tier specification.
     */
    protected function getDefaultFeatureLimit(string $featureSlug)
    {
        $defaults = [
            'visibility_multiplier' => '1.0x',
            'ads_frequency' => 'High',
            'job_priority' => '0',
            'dm_recruiters_msme' => 'No',
            'boost_credits' => '0',
            'ai_tools' => 'No',
            'leaderboard_access' => 'No',
            'custom_url' => 'No',
            'support_level' => 'Basic',
            'tipping_i_shoutpay' => 'No',
            'sell_services' => 'Yes',
            'creator_monetization' => 'No',
            'affiliate_earnings' => 'No',
            'job_referral_earnings' => 'No',
            'ad_revenue_share' => 'No',
            'platform_fee' => '15%',
            'transaction_charges' => 'Standard',
            'withdrawal_system' => 'No',
            'feed_priority' => 'Low',
            'content_reach_cap' => 'Limited',
            'discovery_access' => 'Limited',
            'profile_intent_tag' => 'No',
            'private_groups_access' => 'No',
            'events_access' => 'No',
            // Creator Specific defaults
            'creator_badge' => 'No',
            'visibility_boost_creator' => '1.1x',
            'content_reach_priority' => 'Low',
            'monetization_access' => 'No',
            'tipping_i_shoutpay_creator' => 'No',
            'boost_credits_creator' => '0',
            'creator_analytics' => 'Low basic',
            'local_discovery_listing' => 'No',
            'creator_storefront' => 'No',
            'sell_services_gigs' => 'Yes',
            'upi_payments' => 'No',
            'affiliate_earnings_creator' => 'No',
            'ad_revenue_share_creator' => 'No',
            'ai_content_assistant' => 'No',
            'profile_customization' => 'No',
            'creator_score_trust_rank' => 'No',
            'collab_access' => 'No',
            'brand_deal_visibility' => 'No',
        ];

        return $defaults[$featureSlug] ?? null;
    }
}
