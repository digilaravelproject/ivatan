<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class OrderService
{
    /**
     * securely cancel/reject an order, reverting stock in a transaction
     */
    public function cancelOrder($orderId, $user)
    {
        return DB::transaction(function () use ($orderId, $user) {
            // Lock the order row to prevent concurrent updates
            $order = UserOrder::lockForUpdate()->findOrFail($orderId);

            // Authorization: Admin, Buyer, or direct Seller (Child Order)
            if (!$user->is_admin) {
                if ($user->is_seller && $order->seller_id !== $user->id) {
                    throw new AuthorizationException('You do not own this order.');
                } elseif (!$user->is_seller && $order->buyer_id !== $user->id) {
                    throw new AuthorizationException('You are not authorized to cancel this order.');
                }
            }

            // State Machine logic
            if (in_array($order->status, [UserOrder::STATUS_DELIVERED ?? 'delivered', 'cancelled', 'rejected'])) {
                throw ValidationException::withMessages(['error' => "Order cannot be cancelled because it is already {$order->status}."]);
            }

            // Cancel the Order
            $order->update(['status' => 'cancelled']);

            // Cancel associated Shipping (if exists)
            if ($order->shipping) {
                $order->shipping->update(['status' => 'cancelled']);
            }

            // Cancel all sub-orders if this is a Parent Order
            if ($order->parent_id === null) {
                /** @var \Illuminate\Database\Eloquent\Collection<int, UserOrder> $childOrders */
                $childOrders = UserOrder::where('parent_id', $order->id)->lockForUpdate()->get();
                foreach ($childOrders as $child) {
                    $child->update(['status' => 'cancelled']);
                    if ($child->shipping) {
                        $child->shipping->update(['status' => 'cancelled']);
                    }
                    $this->restockOrderItems($child);
                }
            } else {
                // If it's a child order or a traditional standalone order
                $this->restockOrderItems($order);
            }

            return $order;
        });
    }

    private function restockOrderItems(UserOrder $order)
    {
        // Must load items to restock
        foreach ($order->items as $item) {
            if ($item->item_type === 'user_products') {
                // Lock product row
                $product = UserProduct::lockForUpdate()->find($item->item_id);
                if ($product) {
                    // Safe increment
                    $product->increment('stock', $item->quantity);
                    \Log::info("Restocked Product {$product->id} by {$item->quantity} due to Order {$order->id} cancellation.");
                }
            }
        }
    }
}
