<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserProduct;

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

    /**
     * Get aggregated Dashboard stats for the authenticated Seller
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user || !$user->is_seller) {
                return response()->json(['success' => false, 'message' => 'Unauthorized or not a seller'], 403);
            }

            // Cache the stats for 5 minutes specific to the seller
            $stats = Cache::remember("seller_stats_{$user->id}", 300, function () use ($user) {
                $totalOrders = UserOrder::where('seller_id', $user->id)->count();
                $pendingOrders = UserOrder::where('seller_id', $user->id)->where('status', 'pending')->count();
                $totalRevenue = UserOrder::where('seller_id', $user->id)
                    ->whereIn('payment_status', [UserOrder::PAYMENT_PAID ?? 'paid'])
                    ->sum('total_amount');
                $totalProducts = UserProduct::where('seller_id', $user->id)->count();

                return [
                    'total_orders' => $totalOrders,
                    'pending_orders' => $pendingOrders,
                    'total_revenue' => $totalRevenue,
                    'total_products' => $totalProducts,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Seller Dashboard Stats Error', ['user_id' => $request->user()->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch dashboard stats'], 500);
        }
    }
}
