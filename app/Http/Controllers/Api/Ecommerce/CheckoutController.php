<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CheckoutRequest;
use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserCartItem;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserOrderItem;
use App\Models\Ecommerce\UserPayment;
use App\Models\Ecommerce\UserAddress;
use App\Models\Ecommerce\UserShipping;
use App\Models\Ecommerce\UserProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request): mixed
    {
        $user = $request->user();

        // Fetch user cart with items
        $cart = UserCart::with('items')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        // Begin DB transaction
        return DB::transaction(function () use ($request, $user, $cart): JsonResponse {
            $total = 0;
            $productMap = [];

            // Validate stock, price, existence
            foreach ($cart->items as $ci) {
                $lineTotal = bcmul((string)$ci->price, (string)$ci->quantity, 2);
                $total = bcadd((string)$total, (string)$lineTotal, 2);

                if ($ci->item_type === 'user_products') {
                    $product = UserProduct::lockForUpdate()->find($ci->item_id);

                    if (!$product) {
                        abort(404, "Product not found (ID: {$ci->item_id})");
                    }

                    if ($product->stock < $ci->quantity) {
                        abort(422, "Insufficient stock for product: {$product->title}");
                    }

                    // if ($product->price != $ci->price) {
                    //     abort(409, "Price has changed for product: {$product->title}");
                    // }

                    // if ($product->price != $ci->price) {
                    //     return response()->json([
                    //         'success' => false,
                    //         'error' => 'price_changed',
                    //         'message' => "Price has been updated for product: {$product->title}",
                    //         'old_price' => $ci->price,
                    //         'new_price' => $product->price,
                    //     ], 409);
                    // }

                    if ($product->price != $ci->price) {
    // Auto update cart price
    $ci->update(['price' => $product->price]);

    // Recalculate total
    $lineTotal = bcmul((string)$product->price, (string)$ci->quantity, 2);
    $total = bcadd((string)$total, (string)$lineTotal, 2);
}



                    // Cache product to avoid querying again
                    $productMap[$ci->item_id] = $product;
                }
            }

            // Create order
            $order = UserOrder::create([
                'uuid' => (string) Str::uuid(),
                'buyer_id' => $user->id,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cod' ? 'unpaid' : 'pending',
            ]);

            // Save shipping address
            $addr = $request->input('shipping_address');
            $address = UserAddress::create([
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

            // Create order items and reduce stock
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

                // Reduce product stock
                if ($ci->item_type === 'user_products') {
                    $product = $productMap[$ci->item_id];
                    $product->stock = max(0, $product->stock - $ci->quantity);
                    $product->save();
                }
            }

            // Create payment record
            $payment = UserPayment::create([
                'uuid' => (string) Str::uuid(),
                'order_id' => $order->id,
                'gateway' => $request->payment_method,
                'amount' => $total,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'transaction_id' => null,
                'meta' => json_encode([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]),
            ]);

            // Create shipping placeholder
            $shipping = UserShipping::create([
                'uuid' => (string) Str::uuid(),
                'order_id' => $order->id,
                'provider' => null,
                'tracking_number' => null,
                'status' => 'pending',
                'meta' => null,
            ]);

            // Clear the cart
            $cart->items()->delete();

            // Optionally trigger an event
            // event(new OrderPlaced($order));

            // Load full order details
            $order->load('items', 'address', 'shipping', 'payment');

            // Return response
            return response()->json([
                'success' => true,
                'message' => $request->payment_method === 'cod'
                    ? 'Order placed (Cash on Delivery). Please pay upon delivery.'
                    : 'Order created. Proceed to payment.',
                'order' => $order,
                'payment' => $payment,
            ], 201);
        }, 30); // End transaction
    }
}
