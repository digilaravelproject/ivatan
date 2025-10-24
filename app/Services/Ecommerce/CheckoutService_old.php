<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserOrderItem;
use App\Models\Ecommerce\UserPayment;
use App\Models\Ecommerce\UserAddress;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckoutService
{
    public function checkout(array $data, $user)
    {
        $cart = UserCart::with(['items.userProduct', 'items.userService'])->where('user_id', $user->id)->first();

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
            }

            // Create order and save address
            $order = $this->createOrder($user, $total, $data);
            $this->saveShippingAddress($data['shipping_address'], $order);

            // Create order items
            $this->createOrderItems($cart->items, $order);

            // Create payment record
            $this->createPayment($order, $total, $data['payment_method']);

            // Clear cart
            $cart->items()->delete();

            return [
                'order' => $order,
                'price_updates' => $priceChangedItems,
            ];
        });
    }

    private function validateItem($ci, &$priceChangedItems)
    {
        if ($ci->item_type === 'user_products') {
            $product = UserProduct::withoutGlobalScopes()->lockForUpdate()->find($ci->item_id);
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
        } elseif ($ci->item_type === 'user_services') {
            $service = UserService::withoutGlobalScopes()->lockForUpdate()->find($ci->item_id);
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

    private function createOrder($user, $total, $data)
    {
        return UserOrder::create([
            'uuid' => (string) Str::uuid(),
            'buyer_id' => $user->id,
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
