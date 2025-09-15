<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CartAddRequest;
use App\Http\Requests\Ecommerce\CartUpdateRequest;
use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserCartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cart = UserCart::with('items')
            ->firstOrCreate(['user_id' => $request->user()->id], [
                'uuid' => Str::uuid(),
            ]);

        $totalPrice = $cart->items->sum(fn($item) => $item->price * $item->quantity);

        return response()->json([
            'cart' => $cart,
            'total_price' => $totalPrice,
        ], 200);
    }

    public function add(CartAddRequest $request): JsonResponse
    {
        $cart = UserCart::firstOrCreate(['user_id' => $request->user()->id], [
            'uuid' => Str::uuid(),
        ]);

        // Check if item already exists in cart (same seller_id, item_type, item_id)
        $item = UserCartItem::where('cart_id', $cart->id)
            ->where('seller_id', $request->seller_id)
            ->where('item_type', $request->item_type)
            ->where('item_id', $request->item_id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity ?? 1);
            $item->refresh();
        } else {
            $item = UserCartItem::create([
                'uuid'      => Str::uuid(),
                'cart_id'   => $cart->id,
                'seller_id' => $request->seller_id,
                'item_type' => $request->item_type,
                'item_id'   => $request->item_id,
                'price'     => $request->price,
                'quantity'  => $request->quantity ?? 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'item' => $item,
        ], 201); // 201 Created
    }

    public function update(CartUpdateRequest $request, $id): JsonResponse
    {
        try {
            $item = UserCartItem::where('id', $id)
                ->whereHas('cart', fn($q) => $q->where('user_id', $request->user()->id))
                ->firstOrFail();

            $item->update([
                'quantity' => $request->quantity
            ]);

            $item->refresh(); // Ensure latest data is returned

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated',
                'item' => $item,
            ], 200);
        } catch (\Exception $e) {
            logger('Update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }


    public function remove(Request $request, $id): JsonResponse
    {
        $item = UserCartItem::where('id', $id)
            ->whereHas('cart', fn($q) => $q->where('user_id', $request->user()->id))
            ->firstOrFail();

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ], 204); // 204 No Content (typically no content in response body, but returning message is fine here)
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = UserCart::where('user_id', $request->user()->id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'No cart found',
            ], 404);
        }

        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ], 200);
    }
}
