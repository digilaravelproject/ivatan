<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserShipping;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    protected OrderService $orderService;
    protected NotificationService $notificationService;

    public function __construct(OrderService $orderService, NotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
    }

    /**
     * Update shipping info
     */
    public function updateShipping(int $orderId, array $data, $user)
    {
        $order = UserOrder::findOrFail($orderId);

        // Authorization checks
        if (!$user->is_admin) {
            if (!$user->is_seller) {
                throw new AuthorizationException('You are not authorized to update this shipping info.');
            }

            if ($order->seller_id !== $user->id) {
                $hasItem = \App\Models\Ecommerce\UserOrderItem::where('order_id', $order->id)
                    ->where('seller_id', $user->id)
                    ->exists();

                if (!$hasItem) {
                    throw new AuthorizationException('You are not authorized to update this order’s shipping info.');
                }
            }
        }

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

        $shipping->fill([
            'provider' => $data['provider'] ?? $shipping->provider,
            'tracking_number' => $data['tracking_number'],
            'status' => $data['status'] ?? 'shipped',
            'meta' => isset($data['meta']) ? json_encode($data['meta']) : $shipping->meta,
        ])->save();

        if ($shipping->status === 'cancelled') {
            $this->orderService->cancelOrder($order->id, $user);

            // Notify buyer
            try {
                $buyer = User::find($order->buyer_id);
                if ($buyer) {
                    $this->notificationService->sendToUser($buyer, 'order_cancelled', [
                        'title'       => 'Order Cancelled',
                        'message'     => 'Your order #' . $order->id . ' has been cancelled.',
                        'order_id'    => $order->id,
                        'order_uuid'  => $order->uuid,
                        'action_url'  => null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Cancellation notification failed', ['error' => $e->getMessage()]);
            }
        }

        return $shipping;
    }

    /**
     * Get shipping info by order ID
     */
    public function getShipping(int $orderId, $user)
    {
        $order = UserOrder::where('id', $orderId)
            ->where('buyer_id', $user->id)
            ->firstOrFail();

        $shipping = UserShipping::where('order_id', $order->id)->first();

        if (!$shipping) {
            throw new ModelNotFoundException('Shipping information not available yet.');
        }

        return $shipping;
    }
}
