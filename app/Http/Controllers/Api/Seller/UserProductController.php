<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\StoreUserProductRequest;
use App\Http\Requests\Ecommerce\UpdateUserProductRequest;
use App\Http\Resources\Ecommerce\ProductResource;
use App\Models\Ecommerce\UserProduct;
use App\Models\User;
use App\Services\Ecommerce\SellerProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserProductController extends Controller
{
    public function __construct(
        protected SellerProductService $sellerProductService
    ) {}

    // Get all products for the authenticated seller
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $products = $this->sellerProductService->getSellerProducts($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully.',
                'data' => ProductResource::collection($products->items()),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch seller products', [
                'user_id' => optional($request->user())->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching your products.',
            ], 500);
        }
    }

    // Get products for a specific seller by seller ID
    public function getSellerProducts(Request $request, $sellerId): JsonResponse
    {
        try {
            $seller = User::where('id', $sellerId)
                ->where('is_seller', true)
                ->first();

            if (!$seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seller not found or is not active.',
                ], 404);
            }

            $products = $this->sellerProductService->getSellerProducts($seller->id);

            return response()->json([
                'success' => true,
                'message' => 'Seller products fetched successfully.',
                'data' => ProductResource::collection($products->items()),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch products for seller', [
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching seller products.',
            ], 500);
        }
    }

    // Get details of a specific product
    public function show($productIdentifier): JsonResponse
    {
        try {
            $product = $this->sellerProductService->findProduct($productIdentifier);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found. Please check the product ID or SKU.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching product details', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    // Store a new product
    public function store(StoreUserProductRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $coverImage = $request->file('cover_image');
            $images = $request->file('images', []);
            if (!is_array($images) && $request->hasFile('images')) {
                $images = [$images];
            }

            $product = $this->sellerProductService->createProduct(
                $user,
                $request->only(['title', 'description', 'price', 'discount_price', 'stock']),
                $coverImage,
                $images
            );

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store product', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product. Please try again later.',
            ], 500);
        }
    }

    // Update an existing product
    public function update(UpdateUserProductRequest $request, UserProduct $product): JsonResponse
    {
        try {
            $user = $request->user();

            if ($product->seller_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to product.',
                ], 403);
            }

            $coverImage = $request->file('cover_image');
            $images = $request->file('images', []);
            if (!is_array($images) && $request->hasFile('images')) {
                $images = [$images];
            }

            $removeImageIds = $request->input('remove_image_ids', []);

            $updatedProduct = $this->sellerProductService->updateProduct(
                $user,
                $product,
                $request->only(['title', 'description', 'price', 'discount_price', 'stock', 'status']),
                $coverImage,
                $images,
                $removeImageIds
            );

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => new ProductResource($updatedProduct),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update product', [
                'user_id' => $request->user()?->id,
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product. Please try again later.',
            ], 500);
        }
    }

    // Destroy product
    public function destroy(Request $request, UserProduct $product): JsonResponse
    {
        try {
            $user = $request->user();

            if ($product->seller_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to product.',
                ], 403);
            }

            $this->sellerProductService->deleteProduct($user, $product);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to delete product', [
                'user_id' => $request->user()?->id,
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product. Please try again later.',
            ], 500);
        }
    }
}
