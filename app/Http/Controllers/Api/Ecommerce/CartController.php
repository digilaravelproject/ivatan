<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CartAddRequest;
use App\Http\Requests\Ecommerce\CartUpdateRequest;
use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserCartItem;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CartController extends Controller
{
    /**
     * Get the current user's cart and total price.
     * Apply caching for the cart retrieval.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check if cart is cached; otherwise, retrieve and cache it
            $cart = Cache::remember("cart_user_{$request->user()->id}", 86400, function () use ($request) {
                return $this->getUserCart($request->user()->id);
            });

            $totalPrice = $cart->items->sum(fn($item) => $item->price * $item->quantity);

            return response()->json([
                'cart' => $cart,
                'total_price' => $totalPrice,
            ], 200);
        } catch (Exception $e) {
            logger('Cart retrieval error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart. Please try again later.',
            ], 500);
        }
    }

    /**
     * Add item to the cart, or increment quantity if already exists.
     */
    public function add(CartAddRequest $request): JsonResponse
    {
        /** @var \Illuminate\Http\Request $request */
        try {
            DB::beginTransaction();

            // Retrieve the user's cart
            $cart = $this->getUserCart($request->user()->id);

            // Initialize price, seller_id, and quantity
            $price = null;
            $seller_id = null;
            $quantity = $request->quantity ?? 1; // Default to 1 if no quantity provided
            $itemDetails = null;

            // Fetch product or service details based on item type and item ID
            if ($request->item_type === 'user_products') {
                // Fetch the product details using the item_id
                $product = UserProduct::with('seller')->find($request->item_id);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found.',
                    ], 404);
                }

                // Ensure quantity does not exceed available stock
                if ($quantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry, only {$product->stock} units are available in stock.",
                    ], 400);
                }

                // Assign price, seller_id, and item details
                $price = $product->price;
                $seller_id = $product->seller_id;
                $itemDetails = [
                    'name' => $product->title,
                    'cover_image' => $product->cover_image,
                    'slug' => $product->slug,
                    'product_id' => $product->id,
                    'description' => $product->description,
                    'images' => $product->images,  // Assuming product has images relationship
                    'seller' => $product->seller,  // Seller details (e.g., name, contact)
                ];
            } elseif ($request->item_type === 'user_services') {
                // Fetch the service details using the item_id
                $service = UserService::with('seller')->find($request->item_id);
                if (!$service) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Service not found.',
                    ], 404);
                }

                // No stock check for services, quantity can be null or omitted
                $price = $service->price;
                $seller_id = $service->seller_id;
                $itemDetails = [
                    'name' => $service->title,
                    'cover_image' => $service->cover_image,
                    'slug' => $service->slug,
                    'service_id' => $service->id,
                    'description' => $service->description,
                    'images' => $service->images,  // Assuming service has images relationship
                    'seller' => $service->seller,  // Seller details (e.g., name, contact)
                ];

                // For services, the quantity is irrelevant or can be treated differently
                $quantity = null; // Optional: Set a default for services
            }

            // If seller_id is not found, return an error
            if (!$seller_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seller not found for this item.',
                ], 404);
            }

            // Check if the item already exists in the cart (same seller_id, item_type, item_id)
            $item = UserCartItem::where('cart_id', $cart->id)
                ->where('seller_id', $seller_id)
                ->where('item_type', $request->item_type)
                ->where('item_id', $request->item_id)
                ->first();

            if ($item) {
                // Item already exists, increment the quantity
                if ($item->quantity + $quantity <= ($product ? $product->stock : PHP_INT_MAX)) {
                    $item->increment('quantity', $quantity);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry, you can't add more than {$product->stock} units of this product.",
                    ], 400);
                }
            } else {
                // Create new cart item with fetched price and seller_id
                $item = UserCartItem::create([
                    'uuid' => Str::uuid(),
                    'cart_id' => $cart->id,
                    'seller_id' => $seller_id,
                    'item_type' => $request->item_type,
                    'item_id' => $request->item_id,
                    'price' => $price,
                    'quantity' => $quantity,
                ]);
            }

            // Clear the cache for the cart to ensure it is updated
            Cache::forget("cart_user_{$request->user()->id}");
            $item->refresh();
            DB::commit();

            // Include item details (name, image, seller, etc.) in the response
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'item' => array_merge($item->toArray(), $itemDetails), // Merge the additional details
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            logger('Error adding item to cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the quantity of an item in the user's cart.
     *
     * Validates the new quantity, checks stock availability, updates the cart item,
     * and returns the updated item details along with the status.
     *
     * @param CartUpdateRequest $request The request containing the updated quantity.
     * @param int $id The ID of the cart item to update.
     *
     * @return \Illuminate\Http\JsonResponse The response with a success message or error.
     */
    public function update(CartUpdateRequest $request, $id): JsonResponse
    {
        /** @var \Illuminate\Http\Request $request */
        try {
            DB::beginTransaction();

            // Fetch the cart item and check if it belongs to the authenticated user
            $item = UserCartItem::where('id', $id)
                ->whereHas('cart', fn($q) => $q->where('user_id', $request->user()->id))
                ->firstOrFail();

            // Stock validation for products
            if ($item->item_type === 'user_products') {
                $product = UserProduct::find($item->item_id);
                if ($request->quantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry, only {$product->stock} units are available in stock.",
                    ], 400);
                }
            }
            if ($item->item_type === 'user_services') {
                // No stock validation needed for services
                $request->quantity = null; // Set quantity to null for services
            }
            // Update the quantity in the cart
            $item->update(['quantity' => $request->quantity]);

            // Prepare the item details for the response
            $itemDetails = null;
            if ($item->item_type === 'user_products') {
                $product = UserProduct::find($item->item_id);
                $itemDetails = [
                    'name' => $product->title,
                    'cover_image' => $product->cover_image,
                    'slug' => $product->slug,
                    'product_id' => $product->id,
                    'description' => $product->description,
                    'images' => $product->images,
                    'seller' => $product->seller,  // Seller details
                ];
            } elseif ($item->item_type === 'user_services') {
                $service = UserService::find($item->item_id);
                $itemDetails = [
                    'name' => $service->title,
                    'cover_image' => $service->cover_image,
                    'slug' => $service->slug,
                    'service_id' => $service->id,
                    'description' => $service->description,
                    'images' => $service->images,
                    'seller' => $service->seller,  // Seller details
                ];
            }

            // Clear the user's cart cache to ensure the cart data is fresh
            Cache::forget("cart_user_{$request->user()->id}");

            // Refresh the item and commit the transaction
            $item->refresh();
            DB::commit();

            // Return the response with the updated cart item details
            return response()->json([
                'success' => true,
                'message' => 'Quantity updated successfully',
                'item' => array_merge($item->toArray(), $itemDetails),  // Merge item data with product/service details
            ], 200);
        } catch (Exception $e) {
            // Rollback in case of an error
            DB::rollBack();
            logger('Update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update item. Please try again later.',
            ], 500);
        }
    }


    /**
     * Remove item from the cart.
     */
    public function remove(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Find the item in the cart, ensuring it's associated with the authenticated user
            $item = UserCartItem::where('id', $id)
                ->whereHas('cart', fn($q) => $q->where('user_id', $request->user()->id))
                ->firstOrFail();

            // Get item details (you might want to keep these for future use or logging)
            $itemDetails = [
                'item_id' => $item->item_id,
                'item_type' => $item->item_type,
                'seller_id' => $item->seller_id,
                'quantity' => $item->quantity,
            ];

            // Delete the item from the cart
            $item->delete();

            // Clear the cache for the cart to ensure it is updated
            Cache::forget("cart_user_{$request->user()->id}");

            // Commit the transaction
            DB::commit();

            // Return success response with item details and confirmation message
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'item_id' => $itemDetails['item_id'], // Include the item ID in the response
                'item_type' => $itemDetails['item_type'], // Item type
                'seller_id' => $itemDetails['seller_id'], // Seller ID
                'quantity' => $itemDetails['quantity'], // Item quantity
            ], 200); // 200 OK
        } catch (Exception $e) {
            DB::rollBack();
            logger('Error removing item from cart: ' . $e->getMessage());

            // Handle the error and return a failure response
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart. Please try again later.',
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Clear all items from the user's cart.
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Fetch the user's cart
            $cart = UserCart::where('user_id', $request->user()->id)->first();

            // If no cart exists, return a 404 response
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart found.',
                ], 404);
            }

            // Delete all items from the cart
            $cart->items()->delete();

            // Delete the cart itself
            $cart->delete();

            // Clear the cache to ensure the cart is up-to-date
            Cache::forget("cart_user_{$request->user()->id}");

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart and items cleared successfully.',
            ], 200);
        } catch (Exception $e) {
            // Rollback transaction if an error occurs
            DB::rollBack();
            logger('Error clearing cart: ' . $e->getMessage());

            // Return error response with status 500
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart. Please try again later.',
            ], 500);
        }
    }



    /**
     * Helper function to retrieve the user's cart, creating it if it doesn't exist.
     */
    private function getUserCart(int $userId): UserCart
    {
        try {
            // Check if the cart is cached, if not, retrieve and cache it
            return UserCart::with('items')->firstOrCreate(
                ['user_id' => $userId],
                ['uuid' => Str::uuid()]
            );
        } catch (Exception $e) {
            logger('Error retrieving user cart: ' . $e->getMessage());
            throw new Exception('Failed to retrieve cart');
        }
    }
}
