<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use App\Models\SellerDetail;
use App\Models\UserSubscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->route('profile') ?? $request->get('active_profile');

        if (!$profile) {
            $profileId = $request->route('id') ?? $request->route('profileId');
            if ($profileId) {
                $profile = Profile::with('sellerDetails')->find($profileId);
            }
        }

        if (!$profile || $profile->type !== 'seller') {
            return $next($request);
        }

        $details = $profile->sellerDetails;

        if ($details && $details->seller_type === 'both') {
            $hasActiveSub = UserSubscription::where('profile_id', $profile->id)
                ->whereIn('status', ['active', 'past_due'])
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->exists();

            if (!$hasActiveSub) {
                return response()->json([
                    'status' => false,
                    'message' => 'A subscription is required to sell both products and services. Please purchase a subscription plan.',
                ], 403);
            }
        }

        return $next($request);
    }
}
