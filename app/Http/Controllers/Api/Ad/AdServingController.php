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
     * Fetch one ad for the user (or null if none available) and record impression.
     */
    public function serveAd(Request $request)
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
