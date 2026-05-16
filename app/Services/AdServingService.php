<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\AdImpression;
use Carbon\Carbon;

class AdServingService
{
    /**
     * Fetch one relevant ad for a user (or guest).
     */
    public function getAdForUser($user = null, ?string $ip = null): ?Ad
    {
        $now = Carbon::now();

        // Base query: only active, scheduled ads within start & end date
        $query = Ad::with(['package', 'interests'])
            ->whereIn('status', ['live', 'approved'])
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->whereHas('package', function ($q) {
                $q->where('reach_limit', '>', 0);
            });

        // Interest targeting: match ad interests with user interests
        if ($user && !empty($user->interests)) {
            $userInterestIds = is_string($user->interests) ? json_decode($user->interests, true) : $user->interests;

            if (!empty($userInterestIds)) {
                $query->whereHas('interests', function ($q) use ($userInterestIds) {
                    $q->whereIn('interests.id', $userInterestIds);
                });
            }
        }

        // Package targeting (user_ids or countries)
        // if ($user) {
        //     $query->whereHas('package', function ($q) use ($user) {
        //         $q->where('reach_limit', '>', 0)
        //             ->where(function ($q2) use ($user) {
        //                 $q2->whereJsonContains('targeting->user_ids', $user->id)
        //                     ->orWhereJsonContains('targeting->countries', $user->country ?? '')
        //                     ->orWhereNull('targeting');
        //             });
        //     });
        // }

        // Get 10 random ads and filter reach_limit in PHP to avoid DB complications
        $ads = $query->inRandomOrder()->take(10)->get()
            ->filter(fn($ad) => $ad->impressions < ($ad->package?->reach_limit ?? 0));

        return $ads->isNotEmpty() ? $ads->random() : null;
    }

    /**
     * Record an impression for an ad
     */
    public function recordImpression(Ad $ad, $user = null, ?string $ip = null): AdImpression
    {
        $impression = AdImpression::create([
            'ad_id' => $ad->id,
            'user_id' => $user?->id ?? null,
            'ip_address' => $ip,
        ]);

        // Increment quick counter
        $ad->increment('impressions');

        // If reach_limit reached, mark ad as expired
        if ($ad->package?->reach_limit && $ad->impressions >= $ad->package->reach_limit) {
            $ad->status = 'expired';
            $ad->save();
        }

        return $impression;
    }

    /**
     * Expire ads whose end date has passed
     */
    public function expireAds()
    {
        $now = Carbon::now();
        Ad::where('status', 'live')
            ->where('end_at', '<', $now)
            ->update(['status' => 'expired']);
    }
}
