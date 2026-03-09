<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserOrderItem;
use App\Models\Ecommerce\UserPayment;
use App\Models\Ecommerce\UserAddress;
use App\Models\Ecommerce\UserProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckoutService
{
    public function checkout(array $data, $user)
    {
        // In your CheckoutService
        $cart = UserCart::with(['items.product', 'items.service'])->where('user_id', $user->id)->first();



        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['error' => 'Cart is empty.']);
        }

        return DB::transaction(function () use ($cart, $user, $data) {
            $total = 0;
            $priceChangedItems = [];

            foreach ($cart->items as $ci) {
                $this->validateItem($ci, $priceChangedItems);

                // Calculate total
                $lineTotal = bcmul((string) $ci->price, (string) $ci->quantity, 2);
                $total = bcadd((string) $total, (string) $lineTotal, 2);

                // Deduct stock if it's a product
                if ($ci->item_type === 'user_products') {
                    $this->deductProductStock($ci);
                }
            }

            if (!empty($priceChangedItems)) {
                throw ValidationException::withMessages([
                    'error' => 'Some item prices have changed. Please review your cart.',
                    'price_updates' => $priceChangedItems
                ]);
            }

            // Create parent order and save address
            $parentOrder = $this->createOrder($user, $total, $data);
            $this->saveShippingAddress($data['shipping_address'], $parentOrder);

            // Group items by seller to create Child Orders
            $itemsBySeller = $cart->items->groupBy('seller_id');

            foreach ($itemsBySeller as $sellerId => $items) {
                $sellerTotal = 0;
                foreach ($items as $item) {
                    $itemTotal = bcmul((string) $item->price, (string) $item->quantity, 2);
                    $sellerTotal = bcadd((string) $sellerTotal, (string) $itemTotal, 2);
                }

                // Create Child Order
                $childOrder = $this->createOrder($user, $sellerTotal, $data, $parentOrder->id, $sellerId);

                // Create order items linked to Child Order
                $this->createOrderItems($items, $childOrder);
            }

            // Create payment record linked to Parent Order
            $this->createPayment($parentOrder, $total, $data['payment_method']);

            // Clear cart
            $cart->items()->delete();
            \Illuminate\Support\Facades\Cache::forget("cart_user_{$user->id}");

            return [
                'order' => $parentOrder,
                'price_updates' => $priceChangedItems,
            ];
        });
    }

    private function validateItem($ci, &$priceChangedItems)
    {
        if ($ci->item_type === 'user_products') {
            $product = $ci->product; // Use the relationship here

            if (!$product) {
                throw new HttpException(404, "Product not found (ID: {$ci->item_id})");
            }

            if ($product->stock < $ci->quantity) {
                throw ValidationException::withMessages([
                    'error' => "Insufficient stock for product: {$product->title}"
                ]);
            }

            if ($product->price != $ci->price) {
                $priceChangedItems[] = [
                    'type' => 'product',
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'old_price' => $ci->price,
                    'new_price' => $product->price,
                ];
                $ci->update(['price' => $product->price]);
            }

            if ($priceChangedItems) {
                // Revert price silently to cart update is okay, but we shouldn't continue checkout
                // Will throw after collecting all price changes.
            }
        } elseif ($ci->item_type === 'user_services') {
            $service = $ci->service; // Use the relationship here

            if (!$service) {
                throw new HttpException(404, "Service not found (ID: {$ci->item_id})");
            }

            if ($service->price != $ci->price) {
                $priceChangedItems[] = [
                    'type' => 'service',
                    'service_id' => $service->id,
                    'title' => $service->title,
                    'old_price' => $ci->price,
                    'new_price' => $service->price,
                ];
                $ci->update(['price' => $service->price]);
            }
        } else {
            throw ValidationException::withMessages([
                'error' => 'Unsupported item type: ' . $ci->item_type
            ]);
        }
    }


    private function deductProductStock($ci)
    {
        if ($ci->item_type === 'user_products') {
            // Fetch the latest product data with a row-level lock to prevent race conditions
            $product = UserProduct::where('id', $ci->item_id)->lockForUpdate()->first();

            DB::transaction(function () use ($ci, $product) {
                if ($product) {
                    \Log::info("Before deduction - Product ID: {$product->id}, Available Stock: {$product->stock}, Requested Quantity: {$ci->quantity}");

                    // Check if the requested quantity is available
                    if ($product->stock < $ci->quantity) {
                        throw ValidationException::withMessages([
                            'error' => "Not enough stock available for product: {$product->title}. Only {$product->stock} units available."
                        ]);
                    }

                    // Deduct the stock if enough is available
                    $product->decrement('stock', $ci->quantity);

                    \Log::info("After deduction - Product ID: {$product->id}, New Stock: {$product->stock}");
                } else {
                    // If product is not found, throw a 404 error
                    throw new HttpException(404, "Product not found (ID: {$ci->item_id})");
                }
            });
        }
    }




    private function createOrder($user, $total, $data, $parentId = null, $sellerId = null)
    {
        return UserOrder::create([
            'uuid' => (string) Str::uuid(),
            'buyer_id' => $user->id,
            'parent_id' => $parentId,
            'seller_id' => $sellerId,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => $data['payment_method'] === 'cod' ? 'unpaid' : 'initiated',
        ]);
    }

    private function saveShippingAddress($addr, $order)
    {
        UserAddress::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $order->buyer_id,
            'order_id' => $order->id,
            'type' => 'shipping',
            'name' => $addr['name'],
            'phone' => $addr['phone'],
            'address_line1' => $addr['address_line1'],
            'address_line2' => $addr['address_line2'] ?? null,
            'city' => $addr['city'],
            'state' => $addr['state'],
            'country' => $addr['country'] ?? 'IN',
            'postal_code' => $addr['postal_code'],
        ]);
    }

    private function createOrderItems($items, $order)
    {
        foreach ($items as $ci) {
            UserOrderItem::create([
                'uuid' => (string) Str::uuid(),
                'order_id' => $order->id,
                'seller_id' => $ci->seller_id,
                'item_type' => $ci->item_type,
                'item_id' => $ci->item_id,
                'quantity' => $ci->quantity,
                'price' => $ci->price,
            ]);
        }
    }

    private function createPayment($order, $total, $paymentMethod)
    {
        $requestIp = request()->ip();
        $userAgent = request()->header('User-Agent');

        UserPayment::create([
            'uuid' => (string) Str::uuid(),
            'order_id' => $order->id,
            'gateway' => $paymentMethod,
            'amount' => $total,
            'status' => $paymentMethod === 'cod' ? 'pending' : 'initiated',
            'transaction_id' => null,
            'meta' => json_encode([
                'ip_address' => $requestIp,
                'user_agent' => $userAgent,
            ]),
        ]);
    }
}
