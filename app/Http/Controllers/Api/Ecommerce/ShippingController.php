<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Update shipping info
     */
    public function updateShipping(Request $request, $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'provider' => 'nullable|string|max:100',
                'tracking_number' => 'required|string|max:255',
                'status' => 'nullable|string|in:pending,shipped,in_transit,out_for_delivery,delivered,cancelled',
                'meta' => 'nullable|array',
            ]);

            $shipping = $this->shippingService->updateShipping((int) $orderId, $validated, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Shipping info updated successfully.',
                'shipping' => $shipping,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            Log::error('Shipping update failed', [
                'order_id' => $orderId,
                'user_id' => $request->user()->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping info.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get shipping info
     */
    public function getShipping(Request $request, $orderId): JsonResponse
    {
        try {
            $shipping = $this->shippingService->getShipping((int) $orderId, $request->user());

            return response()->json([
                'success' => true,
                'shipping' => $shipping,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shipping info.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
