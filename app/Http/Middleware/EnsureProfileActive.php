<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $profile = $user->activeProfile;

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'No active profile found. Please create or switch to a profile.',
            ], 403);
        }

        if ($profile->isPending()) {
            return response()->json([
                'status' => false,
                'message' => 'Your profile is pending approval. Please wait for admin approval.',
            ], 403);
        }

        if ($profile->status === 'suspended') {
            return response()->json([
                'status' => false,
                'message' => 'Your profile has been suspended. Contact support for assistance.',
            ], 403);
        }

        $request->merge(['active_profile' => $profile]);

        return $next($request);
    }
}
