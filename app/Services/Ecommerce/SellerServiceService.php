<?php

namespace App\Services\Ecommerce;

use App\Helpers\ImageHelper;
use App\Models\Ecommerce\UserService;
use App\Models\Ecommerce\UserServiceImage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class SellerServiceService
{
    /**
     * Get paginated services for a seller.
     */
    public function getSellerServices(int $sellerId): LengthAwarePaginator
    {
        return UserService::with(['images', 'seller'])
            ->where('seller_id', $sellerId)
            ->latest()
            ->paginate(10);
    }

    /**
     * Find a service by ID or Slug with caching.
     */
    public function findService(string $serviceIdentifier): UserService
    {
        $cacheKey = "service_{$serviceIdentifier}";
        
        $service = Cache::remember($cacheKey, now()->addDay(), function () use ($serviceIdentifier) {
            return UserService::with(['images', 'seller'])
                ->where('id', $serviceIdentifier)
                ->orWhere('slug', $serviceIdentifier)
                ->first();
        });

        if (!$service) {
            throw new ModelNotFoundException("Service not found");
        }

        return $service;
    }

    /**
     * Create a new seller service.
     */
    public function createService(User $user, array $data, ?UploadedFile $coverImage = null, array $images = []): UserService
    {
        return DB::transaction(function () use ($user, $data, $coverImage, $images) {
            $slug = $this->generateUniqueSlug($data['title']);

            $service = UserService::create([
                'uuid'           => (string) Str::uuid(),
                'seller_id'      => $user->id,
                'title'          => $data['title'],
                'slug'           => $slug,
                'description'    => $data['description'] ?? null,
                'price'          => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'status'         => 'pending',
            ]);

            if ($coverImage) {
                $coverImagePath = ImageHelper::uploadEcomImage($coverImage, $user->id, 'services/cover');
                $service->cover_image = $coverImagePath;
                $service->save();
            }

            if (!empty($images)) {
                $this->handleServiceImages($images, $user, $service);
            }

            return $service->load('images')->refresh();
        });
    }

    /**
     * Update an existing service.
     */
    public function updateService(User $user, UserService $service, array $data, ?UploadedFile $coverImage = null, array $images = [], array $removeImageIds = []): UserService
    {
        return DB::transaction(function () use ($user, $service, $data, $coverImage, $images, $removeImageIds) {
            // Check ownership
            if ($service->seller_id !== $user->id) {
                throw new \Exception('Unauthorized', 403);
            }

            // Generate unique slug if title changes
            if (isset($data['title']) && $data['title'] !== $service->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            $service->update($data);

            if ($coverImage) {
                if ($service->cover_image) {
                    ImageHelper::deleteEcomImage($service->cover_image);
                }
                $coverImagePath = ImageHelper::uploadEcomImage($coverImage, $user->id, 'services/cover');
                $service->update(['cover_image' => $coverImagePath]);
            }

            if (!empty($images)) {
                $this->handleServiceImages($images, $user, $service);
            }

            if (!empty($removeImageIds)) {
                $this->removeServiceImages($removeImageIds, $service);
            }

            // Clear cache for this service identifier
            Cache::forget("service_{$service->id}");
            Cache::forget("service_{$service->slug}");

            return $service->load(['images'])->refresh();
        });
    }

    /**
     * Delete a service.
     */
    public function deleteService(User $user, UserService $service): void
    {
        DB::transaction(function () use ($user, $service) {
            if ($service->seller_id !== $user->id) {
                throw new \Exception('Unauthorized', 403);
            }

            // Delete associated images
            if ($service->cover_image) {
                ImageHelper::deleteEcomImage($service->cover_image);
            }

            $images = UserServiceImage::where('service_id', $service->id)->get();
            foreach ($images as $img) {
                ImageHelper::deleteEcomImage($img->image_path);
                $img->delete();
            }

            // Clear caches
            Cache::forget("service_{$service->id}");
            Cache::forget("service_{$service->slug}");

            $service->delete();
        });
    }

    /**
     * Generate unique slug.
     */
    private function generateUniqueSlug(string $title): string
    {
        $slugBase = Str::slug($title) . '-' . Str::random(8);
        $slug = $slugBase;

        while (UserService::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . Str::random(4);
        }

        return $slug;
    }

    /**
     * Handle gallery images.
     */
    private function handleServiceImages(array $images, User $user, UserService $service): void
    {
        foreach ($images as $img) {
            if ($img instanceof UploadedFile) {
                $path = ImageHelper::uploadEcomImage($img, $user->id, 'services/gallery');
                UserServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => $path,
                ]);
            }
        }
    }

    /**
     * Remove gallery images.
     */
    private function removeServiceImages(array $ids, UserService $service): void
    {
        $images = UserServiceImage::whereIn('id', $ids)->where('service_id', $service->id)->get();
        foreach ($images as $img) {
            ImageHelper::deleteEcomImage($img->image_path);
            $img->delete();
        }
    }
}
