<?php

namespace App\Http\Controllers\Api\Seller;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProductRequest;
use App\Http\Requests\UpdateUserProductRequest;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserProductImage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class UserProductController extends Controller
{
    // Get all products for the authenticated seller
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            // Fetch products with related images (eager loaded)
            $products = UserProduct::with('images')
                ->where('seller_id', $user->id)
                ->latest()
                ->simplePaginate(10);


            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully.',
                'data' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to fetch seller products', [
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
            // Step 1: Ensure seller exists
            $seller = User::where('id', $sellerId)
                ->where('is_seller', true)
                ->first();

            if (! $seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seller not found or is not active.',
                ], 404);
            }

            // Step 2: Fetch seller's products
            $products = UserProduct::with('images')
                ->where('seller_id', $seller->id)
                ->latest()
                ->simplePaginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Seller products fetched successfully.',
                'data' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to fetch products for seller', [
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
            // Try to retrieve the product from the cache first (cached for 1 day)
            $cacheKey = "product_{$productIdentifier}"; // Cache key based on product identifier
            $product = Cache::remember($cacheKey, now()->addDay(), function () use ($productIdentifier) {
                // Try fetching the product by either ID or slug (SKU)
                return UserProduct::with(['images', 'seller'])
                    ->where('id', $productIdentifier) // Try finding by ID
                    ->orWhere('slug', $productIdentifier) // Or try finding by slug (SKU)
                    ->first();
            });

            if (!$product) {
                throw new ModelNotFoundException("Product not found");
            }

            return response()->json([
                'success' => true,
                'data' => $product,
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the product wasn't found in both ID and slug queries
            return response()->json([
                'success' => false,
                'message' => 'Product not found. Please check the product ID or SKU.',
            ], 404);
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            \Log::error('Error fetching product details', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    // Store a new product
    /**
     * @param \App\Http\Requests\StoreUserProductRequest $request
     */
    public function store(StoreUserProductRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            /** @var \Illuminate\Http\Request $request */
            $user = $request->user();
            // Generate the base slug
            $slugBase = Str::slug($request->title) . '-' . Str::random(8);
            $slug = $slugBase;

            // Check if the slug already exists and append random string if needed
            $counter = 1;
            while (UserProduct::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . Str::random(4); // Add a random string if slug already exists
                $counter++;
            }

            $product = UserProduct::create([
                'uuid'        => (string) Str::uuid(),
                'seller_id'   => $user->id,
                'title'       => $request->title,
                'slug'        => $slug,
                'description' => $request->description ?? null,
                'price'       => $request->price,
                'stock'       => $request->stock ?? 0,
                'status'      => 'pending',
            ]);
            /** @var \Illuminate\Http\Request $request */

            // Upload cover image
            if ($request->hasFile('cover_image')) {
                $path = ImageHelper::uploadEcomImage($request->file('cover_image'), $user->id, 'products/cover');
                $product->update(['cover_image' => $path]);
            }

            // Upload gallery images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $path = ImageHelper::uploadEcomImage($img, $user->id, 'products/gallery');
                    UserProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }
            // Commit the transaction after all operations are successful
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => $product->load('images'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack(); // Rollback in case of error
            \Log::error('Failed to store product', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product. Please try again later.',
            ], 500);
        }
    }


    // Update an existing product
    /**
     * @param \App\Http\Requests\UpdateUserProductRequest $request
     */
    public function update(UpdateUserProductRequest $request, UserProduct $product): JsonResponse
    {
        DB::beginTransaction();
        try {
            /** @var \Illuminate\Http\Request $request */
            $user = $request->user();

            // Check if the logged-in user is the seller of the product
            if ($product->seller_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to product.',
                ], 403);
            }

            // Only these fields are allowed to be updated
            $updateFields = $request->only(['title', 'description', 'price', 'stock']);

            // Check if any fields are provided for update
            if (empty($updateFields) && !$request->hasFile('cover_image') && !$request->hasFile('images') && !$request->filled('remove_image_ids')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid fields to update.',
                ], 400);
            }

            // If the title is updated, generate a new unique slug
            if (isset($updateFields['title'])) {
                // Generate the base slug
                $slugBase = Str::slug($updateFields['title']) . '-' . Str::random(8);
                $slug = $slugBase;

                // Check if the slug already exists and append random string if needed
                while (UserProduct::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . Str::random(4); // Add a random string if slug already exists
                }

                // Assign the unique slug to the product
                $updateFields['slug'] = $slug;
            }

            // Perform the update on the product
            $product->update($updateFields);

            /** @var \Illuminate\Http\Request $request */
            // Replace cover image if uploaded
            if ($request->hasFile('cover_image')) {
                // Delete old cover image
                ImageHelper::deleteEcomImage($product->cover_image);

                // Upload new cover image
                $coverPath = ImageHelper::uploadEcomImage($request->file('cover_image'), $user->id, 'products/cover');
                $product->update(['cover_image' => $coverPath]);
            }

            // Add new gallery images if any
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $galleryPath = ImageHelper::uploadEcomImage($img, $user->id, 'products/gallery');
                    UserProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $galleryPath,
                    ]);
                }
            }

            // Remove selected gallery images if requested
            if ($request->filled('remove_image_ids')) {
                $ids = $request->input('remove_image_ids', []);
                $images = UserProductImage::whereIn('id', $ids)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($images as $img) {
                    ImageHelper::deleteEcomImage($img->image_path);
                    $img->delete();
                }
            }

            // Reload the product from the database with the updated data
            $product->load(['images'])->refresh(); // This will load the images relation and refresh the model

            // Commit the transaction after all operations are successful
            DB::commit();
            // Send a response with the updated product details
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => $product,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack(); // Rollback in case of error
            // Log error and return a failure response
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





    // destroy (also delete images)

    public function destroy(Request $request, UserProduct $product): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            if ($product->seller_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to product.',
                ], 403);
            }

            // Delete cover image
            if ($product->cover_image) {
                ImageHelper::deleteEcomImage($product->cover_image);
            }

            // Delete gallery images
            foreach ($product->images as $img) {
                ImageHelper::deleteEcomImage($img->image_path);
                $img->delete();
            }

            $product->delete();

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
