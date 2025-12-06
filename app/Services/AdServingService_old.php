<?php


namespace App\Services;


use App\Models\Ad;
use App\Models\AdImpression;
use Illuminate\Support\Str;


class AdServingService_old
{
    /**
     * Get one ad to show for the user (simplified selection algorithm).
     */

    public function getAdForUser($user = null, ?string $ip = null): ?Ad
    {
        // Start by building the base query: active ads with package having reach_limit > 0
        $query = Ad::active()
            ->with('package')
            ->whereHas('package', function ($q) {
                $q->where('reach_limit', '>', 0);
            });

        // Optional: Pre-filter by targeting in SQL if possible (only for basic stuff like country)
        // We do full targeting filtering in PHP after retrieving a few random ads
        $ads = $query->inRandomOrder()->take(10)->get(); // Fetch up to 10 random ads

        $filteredAds = $ads->filter(function ($ad) use ($user) {
            // Check reach limit
            $reach = $ad->package?->reach_limit ?? 0;
            if ($reach > 0 && $ad->impressions >= $reach) {
                return false;
            }

            // Targeting logic
            $targeting = $ad->package->targeting ?? [];

            // No targeting? Show ad to everyone
            if (empty($targeting)) return true;

            // Match user_ids
            if (isset($targeting['user_ids']) && $user) {
                if (!in_array($user->id, $targeting['user_ids'])) {
                    return false;
                }
            }

            // Match country
            if (isset($targeting['countries']) && $user?->country) {
                if (!in_array($user->country, $targeting['countries'])) {
                    return false;
                }
            }

            return true;
        });

        // Return a random one from valid options
        return $filteredAds->isNotEmpty() ? $filteredAds->random() : null;
    }


    // public function getAdForUser($user = null, $ip = null): ?Ad
    // {
    //     // pick random live ad that still has impressions left
    //     $ad = Ad::active()
    //         ->with('package')
    //         ->whereHas('package', function ($q) {
    //             $q->where('reach_limit', '>', 0);
    //         })
    //         ->inRandomOrder()
    //         ->first();


    //     if (! $ad) {
    //         return null;
    //     }


    //     // double-check reach limit
    //     $reach = $ad->package?->reach_limit ?? 0;
    //     if ($reach > 0 && $ad->impressions >= $reach) {
    //         return null;
    //     }


    //     return $ad;
    // }


    /**
     * Record an impression once ad is actually shown to user.
     */
    public function recordImpression(Ad $ad, $user = null, ?string $ip = null): AdImpression
    {
        $impression = AdImpression::create([
            'ad_id' => $ad->id,
            'user_id' => $user?->id ?? null,
            'ip_address' => $ip,
        ]);


        // increment quick counter
        $ad->increment('impressions');


        return $impression;
    }
}
