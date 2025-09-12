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
        $cart = UserCart::with('items')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['error' => 'Cart is empty.']);
        }

        return DB::transaction(function () use ($cart, $user, $data) {
            $total = 0;
            $priceChangedItems = [];

            // Validate products and update cart prices if changed
            foreach ($cart->items as $ci) {
                if ($ci->item_type === 'user_products') {
                    if ($ci->item_type !== 'user_products') {
                        throw ValidationException::withMessages([
                            'error' => 'Unsupported item type: ' . $ci->item_type
                        ]);
                    }

                    $product = UserProduct::withoutGlobalScopes()
                        ->lockForUpdate()
                        ->find($ci->item_id);


                    if (!$product) {
                        throw new HttpException(404, "Product not found (ID: {$ci->item_id})");
                    }

                    // if (!$product->is_active ?? false) {
                    //     throw ValidationException::withMessages([
                    //         'error' => "Product is no longer available: {$product->title}"
                    //     ]);
                    // }

                    if ($product->stock < $ci->quantity) {
                        throw ValidationException::withMessages([
                            'error' => "Insufficient stock for product: {$product->title}"
                        ]);
                    }

                    if ($product->price != $ci->price) {
                        $priceChangedItems[] = [
                            'product_id' => $product->id,
                            'title' => $product->title,
                            'old_price' => $ci->price,
                            'new_price' => $product->price,
                        ];
                        $ci->update(['price' => $product->price]);
                    }
                }
                $lineTotal = bcmul((string) $ci->price, (string) $ci->quantity, 2);
                $total = bcadd((string) $total, (string) $lineTotal, 2);
            }

            // Create Order
            $order = UserOrder::create([
                'uuid' => (string) Str::uuid(),
                'buyer_id' => $user->id,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => $data['payment_method'] === 'cod' ? 'unpaid' : 'initiated',
            ]);

            // Save shipping address
            $addr = $data['shipping_address'];
            UserAddress::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
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

            // Create Order Items
            foreach ($cart->items as $ci) {
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

            $requestIp = request()->ip();
            $userAgent = request()->header('User-Agent');

            // Create Payment record
            UserPayment::create([
                'uuid' => (string) Str::uuid(),
                'order_id' => $order->id,
                'gateway' => $data['payment_method'],
                'amount' => $total,
                'status' => $data['payment_method'] === 'cod' ? 'pending' : 'initiated',
                'transaction_id' => null,
                'meta' => json_encode([
                    'ip_address' => $requestIp,
                    'user_agent' => $userAgent,
                ]),
            ]);

            // Clear cart
            $cart->items()->delete();

            return [
                'order' => $order,
                'price_updates' => $priceChangedItems,
            ];
        });
    }
}
