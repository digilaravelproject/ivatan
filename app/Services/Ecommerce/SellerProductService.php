<?php

namespace App\Services\Ecommerce;

use App\Helpers\ImageHelper;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserProductImage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class SellerProductService
{
    /**
     * Get paginated products for a seller.
     */
    public function getSellerProducts(int $sellerId): LengthAwarePaginator
    {
        return UserProduct::with(['images', 'seller'])
            ->where('seller_id', $sellerId)
            ->latest()
            ->paginate(10);
    }

    /**
     * Find a product by ID or Slug with caching.
     */
    public function findProduct(string $productIdentifier): UserProduct
    {
        $cacheKey = "product_{$productIdentifier}";
        
        $product = Cache::remember($cacheKey, now()->addDay(), function () use ($productIdentifier) {
            return UserProduct::with(['images', 'seller'])
                ->where('id', $productIdentifier)
                ->orWhere('slug', $productIdentifier)
                ->first();
        });

        if (!$product) {
            throw new ModelNotFoundException("Product not found");
        }

        return $product;
    }

    /**
     * Create a new product.
     */
    public function createProduct(User $user, array $data, ?UploadedFile $coverImage = null, array $images = []): UserProduct
    {
        return DB::transaction(function () use ($user, $data, $coverImage, $images) {
            $slug = $this->generateUniqueSlug($data['title']);

            $product = UserProduct::create([
                'uuid'           => (string) Str::uuid(),
                'seller_id'      => $user->id,
                'title'          => $data['title'],
                'slug'           => $slug,
                'description'    => $data['description'] ?? null,
                'price'          => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'stock'          => $data['stock'] ?? 0,
                'status'         => 'pending',
            ]);

            if ($coverImage) {
                $path = ImageHelper::uploadEcomImage($coverImage, $user->id, 'products/cover');
                $product->update(['cover_image' => $path]);
            }

            if (!empty($images)) {
                $this->handleProductImages($images, $user, $product);
            }

            return $product->load('images')->refresh();
        });
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(User $user, UserProduct $product, array $data, ?UploadedFile $coverImage = null, array $images = [], array $removeImageIds = []): UserProduct
    {
        return DB::transaction(function () use ($user, $product, $data, $coverImage, $images, $removeImageIds) {
            // Re-fetch product with a pessimistic lock to ensure secure concurrent stock updates
            $product = UserProduct::lockForUpdate()->findOrFail($product->id);

            // Check ownership
            if ($product->seller_id !== $user->id) {
                throw new \Exception('Unauthorized access to product.', 403);
            }

            // Generate unique slug if title changes
            if (isset($data['title']) && $data['title'] !== $product->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            $product->update($data);

            if ($coverImage) {
                if ($product->cover_image) {
                    ImageHelper::deleteEcomImage($product->cover_image);
                }
                $coverPath = ImageHelper::uploadEcomImage($coverImage, $user->id, 'products/cover');
                $product->update(['cover_image' => $coverPath]);
            }

            if (!empty($images)) {
                $this->handleProductImages($images, $user, $product);
            }

            if (!empty($removeImageIds)) {
                $this->removeProductImages($removeImageIds, $product);
            }

            // Clear cache
            Cache::forget("product_{$product->id}");
            Cache::forget("product_{$product->slug}");

            return $product->load(['images'])->refresh();
        });
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(User $user, UserProduct $product): void
    {
        DB::transaction(function () use ($user, $product) {
            if ($product->seller_id !== $user->id) {
                throw new \Exception('Unauthorized access to product.', 403);
            }

            // Delete associated images
            if ($product->cover_image) {
                ImageHelper::deleteEcomImage($product->cover_image);
            }

            $images = UserProductImage::where('product_id', $product->id)->get();
            foreach ($images as $img) {
                ImageHelper::deleteEcomImage($img->image_path);
                $img->delete();
            }

            // Clear caches
            Cache::forget("product_{$product->id}");
            Cache::forget("product_{$product->slug}");

            $product->delete();
        });
    }

    /**
     * Generate unique slug.
     */
    private function generateUniqueSlug(string $title): string
    {
        $slugBase = Str::slug($title) . '-' . Str::random(8);
        $slug = $slugBase;

        while (UserProduct::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . Str::random(4);
        }

        return $slug;
    }

    /**
     * Handle gallery images.
     */
    private function handleProductImages(array $images, User $user, UserProduct $product): void
    {
        foreach ($images as $img) {
            if ($img instanceof UploadedFile) {
                $path = ImageHelper::uploadEcomImage($img, $user->id, 'products/gallery');
                UserProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }
    }

    /**
     * Remove gallery images.
     */
    private function removeProductImages(array $ids, UserProduct $product): void
    {
        $images = UserProductImage::whereIn('id', $ids)
            ->where('product_id', $product->id)
            ->get();

        foreach ($images as $img) {
            ImageHelper::deleteEcomImage($img->image_path);
            $img->delete();
        }
    }
}
