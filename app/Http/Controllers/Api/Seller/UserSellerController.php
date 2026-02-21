<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserSellerController extends Controller
{
    /**
     * Toggle the authenticated user's seller status.
     */
    public function toggleSelf(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            // Toggle seller status
            $user->is_seller = ! $user->is_seller;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => $user->is_seller
                    ? 'Seller mode activated.'
                    : 'Seller mode deactivated.',
                'data' => [
                    'is_seller' => $user->is_seller,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to toggle seller status', [
                'user_id' => optional($request->user())->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while toggling seller status. Please try again later.',
            ], 500);
        }
    }
}
