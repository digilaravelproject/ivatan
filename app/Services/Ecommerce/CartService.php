<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserCartItem;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CartService
{
    /**
     * Get user cart with formatted items, total price, and total items
     */
    public function getCart($user)
    {
        $cart = Cache::remember("cart_user_{$user->id}", 86400, function () use ($user) {
            return UserCart::with([
                'items.product.images',
                'items.product.seller',
                'items.service.images',
                'items.service.seller'
            ])->firstOrCreate(
                ['user_id' => $user->id],
                ['uuid' => Str::uuid()]
            );
        });

        $totalPrice = $cart->items->sum(fn($item) => $item->price * ($item->quantity ?? 1));
        $totalItems = $cart->items->sum('quantity');

        $formattedItems = $cart->items->map(function ($item) {
            $itemDetails = [];
            if ($item->item_type === 'user_products' && $item->product) {
                $itemDetails = [
                    'name' => $item->product->title,
                    'cover_image' => $item->product->cover_image,
                    'slug' => $item->product->slug,
                    'product_id' => $item->product->id,
                    'description' => $item->product->description,
                    'images' => $item->product->images,
                    'seller' => $item->product->seller,
                ];
            } elseif ($item->item_type === 'user_services' && $item->service) {
                $itemDetails = [
                    'name' => $item->service->title,
                    'cover_image' => $item->service->cover_image,
                    'slug' => $item->service->slug,
                    'service_id' => $item->service->id,
                    'description' => $item->service->description,
                    'images' => $item->service->images,
                    'seller' => $item->service->seller,
                ];
            }
            return array_merge($item->toArray(), $itemDetails);
        });

        $cartData = $cart->toArray();
        $cartData['items'] = $formattedItems;

        return [
            'cart' => $cartData,
            'total_price' => $totalPrice,
            'total_items' => $totalItems,
        ];
    }

    /**
     * Add item to cart
     */
    public function addItem($user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = $this->getUserCart($user->id);

            $price = null;
            $seller_id = null;
            $quantity = $data['quantity'] ?? 1;
            $itemDetails = null;

            if ($data['item_type'] === 'user_products') {
                // Fetch active or approved product
                $product = UserProduct::with('seller')
                    ->whereIn('status', ['active', 'approved'])
                    ->find($data['item_id']);

                if (!$product) {
                    throw new Exception('Product not found or not active.', 404);
                }

                if ($quantity > $product->stock) {
                    throw new Exception("Sorry, only {$product->stock} units are available in stock.", 400);
                }

                $price = $product->price;
                $seller_id = $product->seller_id;
                $itemDetails = [
                    'name' => $product->title,
                    'cover_image' => $product->cover_image,
                    'slug' => $product->slug,
                    'product_id' => $product->id,
                    'description' => $product->description,
                    'images' => $product->images,
                    'seller' => $product->seller,
                ];
            } elseif ($data['item_type'] === 'user_services') {
                // Fetch active or approved service
                $service = UserService::with('seller')
                    ->whereIn('status', ['active', 'approved'])
                    ->find($data['item_id']);

                if (!$service) {
                    throw new Exception('Service not found or not active.', 404);
                }

                $price = $service->price;
                $seller_id = $service->seller_id;
                $itemDetails = [
                    'name' => $service->title,
                    'cover_image' => $service->cover_image,
                    'slug' => $service->slug,
                    'service_id' => $service->id,
                    'description' => $service->description,
                    'images' => $service->images,
                    'seller' => $service->seller,
                ];
                $quantity = 1; // Services quantity is always 1
            }

            if ($seller_id === $user->id) {
                throw new Exception('You cannot purchase your own products or services.', 403);
            }

            if (!$seller_id) {
                throw new Exception('Seller not found for this item.', 404);
            }

            $item = UserCartItem::where('cart_id', $cart->id)
                ->where('seller_id', $seller_id)
                ->where('item_type', $data['item_type'])
                ->where('item_id', $data['item_id'])
                ->first();

            if ($item) {
                $maxStock = isset($product) ? $product->stock : PHP_INT_MAX;
                if ($item->quantity + $quantity <= $maxStock) {
                    $item->increment('quantity', $quantity);
                } else {
                    throw new Exception("Sorry, you can't add more than {$maxStock} units of this product.", 400);
                }
            } else {
                $item = UserCartItem::create([
                    'uuid' => Str::uuid(),
                    'cart_id' => $cart->id,
                    'seller_id' => $seller_id,
                    'item_type' => $data['item_type'],
                    'item_id' => $data['item_id'],
                    'price' => $price,
                    'quantity' => $quantity,
                ]);
            }

            Cache::forget("cart_user_{$user->id}");
            $item->refresh();

            return array_merge($item->toArray(), $itemDetails);
        });
    }

    /**
     * Update cart item quantity
     */
    public function updateItem($user, int $itemId, int $quantity)
    {
        return DB::transaction(function () use ($user, $itemId, $quantity) {
            $item = UserCartItem::where('id', $itemId)
                ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
                ->firstOrFail();

            if ($item->item_type === 'user_products') {
                $product = UserProduct::find($item->item_id);
                if (!$product) {
                    throw new Exception('Product not found.', 404);
                }
                if ($quantity > $product->stock) {
                    throw new Exception("Sorry, only {$product->stock} units are available in stock.", 400);
                }
            }

            $quantityToUpdate = $item->item_type === 'user_services' ? 1 : $quantity;
            $item->update(['quantity' => $quantityToUpdate]);

            $itemDetails = [];
            if ($item->item_type === 'user_products' && isset($product)) {
                $itemDetails = [
                    'name' => $product->title,
                    'cover_image' => $product->cover_image,
                    'slug' => $product->slug,
                    'product_id' => $product->id,
                    'description' => $product->description,
                    'images' => $product->images,
                    'seller' => $product->seller,
                ];
            } elseif ($item->item_type === 'user_services') {
                $service = UserService::find($item->item_id);
                if ($service) {
                    $itemDetails = [
                        'name' => $service->title,
                        'cover_image' => $service->cover_image,
                        'slug' => $service->slug,
                        'service_id' => $service->id,
                        'description' => $service->description,
                        'images' => $service->images,
                        'seller' => $service->seller,
                    ];
                }
            }

            Cache::forget("cart_user_{$user->id}");
            $item->refresh();

            return array_merge($item->toArray(), $itemDetails);
        });
    }

    /**
     * Remove item from cart
     */
    public function removeItem($user, int $itemId)
    {
        return DB::transaction(function () use ($user, $itemId) {
            $item = UserCartItem::where('id', $itemId)
                ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
                ->firstOrFail();

            $itemDetails = [
                'item_id' => $item->item_id,
                'item_type' => $item->item_type,
                'seller_id' => $item->seller_id,
                'quantity' => $item->quantity,
            ];

            $item->delete();
            Cache::forget("cart_user_{$user->id}");

            return $itemDetails;
        });
    }

    /**
     * Clear all items in cart
     */
    public function clearCart($user)
    {
        return DB::transaction(function () use ($user) {
            $cart = UserCart::where('user_id', $user->id)->first();
            if (!$cart) {
                throw new Exception('No cart found.', 404);
            }

            $cart->items()->delete();
            $cart->delete();
            Cache::forget("cart_user_{$user->id}");

            return true;
        });
    }

    /**
     * Get or create cart helper
     */
    private function getUserCart(int $userId): UserCart
    {
        return UserCart::with('items')->firstOrCreate(
            ['user_id' => $userId],
            ['uuid' => Str::uuid()]
        );
    }
}
