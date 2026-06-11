<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\MarketplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarketplaceController extends Controller
{
    protected MarketplaceService $marketplaceService;

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    /**
     * Get all products for the marketplace
     */
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $user = $request->user('sanctum');
            $filters = $request->only(['search']);
            $products = $this->marketplaceService->getProducts($filters, $user);

            return response()->json([
                'success' => true,
                'message' => 'Marketplace products fetched successfully.',
                'data' => $products
            ]);
        } catch (\Throwable $e) {
            Log::error('Marketplace product fetch error: ' . $e->getMessage());
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
            $filters = $request->only(['search']);
            $services = $this->marketplaceService->getServices($filters, $user);

            return response()->json([
                'success' => true,
                'message' => 'Marketplace services fetched successfully.',
                'data' => $services
            ]);
        } catch (\Throwable $e) {
            Log::error('Marketplace service fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching services.',
            ], 500);
        }
    }

    /**
     * Get approved products for a specific user/seller
     */
    public function getUserProducts(Request $request, $userId): JsonResponse
    {
        try {
            $products = $this->marketplaceService->getUserProducts((int) $userId);

            return response()->json([
                'success' => true,
                'message' => 'User products fetched successfully.',
                'data' => $products
            ]);
        } catch (\Throwable $e) {
            Log::error('User products fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user products.',
            ], 500);
        }
    }

    /**
     * Get approved services for a specific user/seller
     */
    public function getUserServices(Request $request, $userId): JsonResponse
    {
        try {
            $services = $this->marketplaceService->getUserServices((int) $userId);

            return response()->json([
                'success' => true,
                'message' => 'User services fetched successfully.',
                'data' => $services
            ]);
        } catch (\Throwable $e) {
            Log::error('User services fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user services.',
            ], 500);
        }
    }
}
