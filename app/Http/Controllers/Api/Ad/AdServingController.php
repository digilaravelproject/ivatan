<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Controller;
use App\Services\AdServingService;
use Illuminate\Http\Request;

class AdServingController extends Controller
{
    protected AdServingService $adService;

    public function __construct(AdServingService $adService)
    {
        $this->adService = $adService;
    }

    /**
     * Serve one relevant ad for the user (or guest) and record impression.
     */

    public function serveAd(Request $request)
    {
        $user = $request->user(); // null if guest
        $ip = $request->ip();

        // Step 1: Expire any ads whose end date has passed
        $this->adService->expireAds();

        // Step 2: Fetch one relevant ad based on interests, package targeting, schedule, and reach_limit
        $ad = $this->adService->getAdForUser($user, $ip);

        if (!$ad) {
            return response()->json([
                'ad' => null,
                'message' => 'No ad available'
            ]);
        }

        // Step 3: Record impression
        $this->adService->recordImpression($ad, $user, $ip);

        // Step 4: Return structured response
        return response()->json([
            'ad' => [
                'id' => $ad->id,
                'title' => $ad->title,
                'description' => $ad->description,
                'media' => $ad->media,
                'start_at' => $ad->start_at,
                'end_at' => $ad->end_at,
                'status' => $ad->status,
                'package' => [
                    'id' => $ad->package?->id,
                    'name' => $ad->package?->name,
                    'price' => $ad->package?->price,
                    'duration_days' => $ad->package?->duration_days,
                ],
                'interests' => $ad->interests->pluck('name'),
            ]
        ]);
    }

    /**
     * Fetch one ad for the user (or null if none available) and record impression.
     */
    public function serveAd_old(Request $request)
    {
        $user = $request->user(); // optional, null for guest
        $ip = $request->ip();

        // Get one live ad
        $ad = $this->adService->getAdForUser($user, $ip);

        if (!$ad) {
            return response()->json(['ad' => null, 'message' => 'No ad available']);
        }

        // Record impression
        $this->adService->recordImpression($ad, $user, $ip);

        return response()->json([
            'ad' => [
                'id' => $ad->id,
                'title' => $ad->title,
                'description' => $ad->description,
                'media' => $ad->media,
                'package' => [
                    'id' => $ad->package?->id,
                    'name' => $ad->package?->name,
                    'price' => $ad->package?->price,
                    'duration_days' => $ad->package?->duration_days,
                ]
            ]
        ]);
    }
}
