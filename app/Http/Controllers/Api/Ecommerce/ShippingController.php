<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserShipping;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Ecommerce\OrderService;

/**
 * Class ShippingController
 *
 * Handles shipping management for orders in the eCommerce module.
 *
 * Responsibilities:
 * - Admin & seller authorization for shipping updates
 * - Buyer access for viewing shipping status
 *
 * @package App\Http\Controllers\Api\Ecommerce
 */
class ShippingController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Update shipping info (tracking number, status) - Only seller or admin can update
     */
    /**
     * Update the shipping information for an order.
     *
     * 🧠 Logic:
     * - Admin can always update.
     * - Seller can update only if they own at least one item in the order.
     * - Buyers or other sellers are not authorized.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string $orderId The ID of the order whose shipping info is being updated.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateShipping(Request $request, $orderId): JsonResponse
    {
        try {
            // 🧾 Validate input data
            $validated = $request->validate([
                'provider' => 'nullable|string|max:100',
                'tracking_number' => 'required|string|max:255',
                'status' => 'nullable|string|in:pending,shipped,in_transit,out_for_delivery,delivered,cancelled',
                'meta' => 'nullable|array',
            ]);

            // 🔎 Fetch order or throw 404
            $order = UserOrder::findOrFail($orderId);
            $user = $request->user();

            /**
             * 🛡️ Authorization
             * - Admin always allowed
             * - Seller allowed only if owns at least one item in the order
             */
            if (!$user->is_admin) {
                if (!$user->is_seller) {
                    throw new AuthorizationException('You are not authorized to update this shipping info.');
                }

                if ($order->seller_id !== $user->id) {
                    // Fallback to checking items to support legacy orders (before sub-orders feature)
                    $hasItem = \App\Models\Ecommerce\UserOrderItem::where('order_id', $order->id)
                        ->where('seller_id', $user->id)
                        ->exists();

                    if (!$hasItem) {
                        throw new AuthorizationException('You are not authorized to update this order’s shipping info.');
                    }
                }
            }

            // 📦 Create or find shipping record
            $shipping = UserShipping::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'status' => 'pending',
                    'provider' => null,
                    'tracking_number' => null,
                    'meta' => null,
                ]
            );

            // ✏️ Update shipping info
            $shipping->fill([
                'provider' => $validated['provider'] ?? $shipping->provider,
                'tracking_number' => $validated['tracking_number'],
                'status' => $validated['status'] ?? 'shipped',
                'meta' => isset($validated['meta']) ? json_encode($validated['meta']) : $shipping->meta,
            ])->save();

            // 🛑 Catch Order Cancellation Flow
            if ($shipping->status === 'cancelled') {
                $this->orderService->cancelOrder($order->id, $user);
            }

            // ✅ Successful response
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
            // 🪵 Log unexpected errors for debugging
            \Log::error('Shipping update failed', [
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
     * Get shipping info by order for buyer only
     * @param Request $request
     * @param int $orderId
     * @return JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function getShipping(Request $request, $orderId): JsonResponse
    {
        try {
            $order = UserOrder::where('id', $orderId)
                ->where('buyer_id', $request->user()->id)
                ->firstOrFail();

            $shipping = UserShipping::where('order_id', $order->id)->first();

            if (!$shipping) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipping information not available yet.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'shipping' => $shipping,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or you are not authorized to view this shipping info.'
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
