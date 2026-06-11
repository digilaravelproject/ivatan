<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CartAddRequest;
use App\Http\Requests\Ecommerce\CartUpdateRequest;
use App\Services\Ecommerce\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display user's cart
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cartData = $this->cartService->getCart($request->user());

            return response()->json($cartData, 200);
        } catch (Exception $e) {
            Log::error('Cart retrieval error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart. Please try again later.',
            ], 500);
        }
    }

    /**
     * Add item to the cart
     */
    public function add(CartAddRequest $request): JsonResponse
    {
        try {
            $item = $this->cartService->addItem($request->user(), $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'item' => $item,
            ], 201);
        } catch (Exception $e) {
            Log::error('Error adding item to cart: ' . $e->getMessage());
            
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 500 ? 'Failed to add item to cart. Please try again later.' : $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Update the quantity of an item in the user's cart.
     */
    public function update(CartUpdateRequest $request, $id): JsonResponse
    {
        try {
            $item = $this->cartService->updateItem($request->user(), (int) $id, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated successfully',
                'item' => $item,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        } catch (Exception $e) {
            Log::error('Update error: ' . $e->getMessage());

            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 500 ? 'Failed to update item. Please try again later.' : $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Remove item from the cart.
     */
    public function remove(Request $request, $id): JsonResponse
    {
        try {
            $details = $this->cartService->removeItem($request->user(), (int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'item_id' => $details['item_id'],
                'item_type' => $details['item_type'],
                'seller_id' => $details['seller_id'],
                'quantity' => $details['quantity'],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        } catch (Exception $e) {
            Log::error('Error removing item from cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart. Please try again later.',
            ], 500);
        }
    }

    /**
     * Clear all items from the user's cart.
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $this->cartService->clearCart($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Cart and items cleared successfully.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());

            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 500 ? 'Failed to clear cart. Please try again later.' : $e->getMessage(),
            ], $status);
        }
    }
}
