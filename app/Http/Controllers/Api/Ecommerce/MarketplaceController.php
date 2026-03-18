<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Get all products for the marketplace
     */
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $user = $request->user('sanctum');
            $query = UserProduct::with(['images', 'seller'])
                ->where('status', 'active');

            // Exclude items belonging to the current user
            if ($user) {
                $query->where('seller_id', '!=', $user->id);
            }

            // Optional search by title
            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $products = $query->latest()->paginate(12);

            return response()->json([
                'success' => true,
                'message' => 'Marketplace products fetched successfully.',
                'data' => $products
            ]);
        } catch (\Throwable $e) {
            \Log::error('Marketplace product fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching products.',
            ], 500);
        }
    }

    /**
     * Get all services for the marketplace
     */
    public function getServices(Request $request): JsonResponse
    {
        try {
            $user = $request->user('sanctum');
            $query = UserService::with(['images', 'seller'])
                ->where('status', 'active');

            // Exclude items belonging to the current user
            if ($user) {
                $query->where('seller_id', '!=', $user->id);
            }

            // Optional search by title
            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $services = $query->latest()->paginate(12);

            return response()->json([
                'success' => true,
                'message' => 'Marketplace services fetched successfully.',
                'data' => $services
            ]);
        } catch (\Throwable $e) {
            \Log::error('Marketplace service fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching services.',
            ], 500);
        }
    }
}
