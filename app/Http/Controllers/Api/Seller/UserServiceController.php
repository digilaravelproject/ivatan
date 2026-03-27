<?php

namespace App\Http\Controllers\Api\Seller;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\StoreUserServiceRequest;
use App\Http\Requests\Ecommerce\UpdateUserServiceRequest;
use App\Models\Ecommerce\UserService;
use App\Models\Ecommerce\UserServiceImage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserServiceController extends Controller
{
    /**
     * Get services for the authenticated seller
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }

            $services = UserService::with(['images', 'seller'])
                ->where('seller_id', $user->id)
                ->latest()
                ->paginate(10);

            return $this->successResponse('Services fetched successfully.', $services);
        } catch (\Throwable $e) {
            \Log::error('Failed to fetch seller services', [
                'user_id' => optional($request->user())->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Something went wrong while fetching your services.', 500);
        }
    }

    /**
     * Get services for a specific seller by seller ID
     */
    public function getSellerServices(Request $request, $sellerId): JsonResponse
    {
        try {
            $seller = User::where('id', $sellerId)
                ->where('is_seller', true)
                ->first();

            if (!$seller) {
                return $this->errorResponse('Seller not found or is not active.', 404);
            }

            $services = UserService::with(['images', 'seller'])
                ->where('seller_id', $seller->id)
                ->latest()
                ->paginate(10);

            return $this->successResponse('Seller services fetched successfully.', $services);
        } catch (\Throwable $e) {
            \Log::error('Failed to fetch services for seller', [
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('An error occurred while fetching seller services.', 500);
        }
    }

    /**
     * Store a new service
     */
    public function store(StoreUserServiceRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            /** @var \Illuminate\Http\Request $request */
            $user = $request->user();
            $slug = $this->generateUniqueSlug($request->title);

            // Create the service
            $service = UserService::create([
                'uuid'        => (string) Str::uuid(),
                'seller_id'   => $user->id,
                'title'       => $request->title,
                'slug'        => $slug,
                'description' => $request->description ?? null,
                'price'       => $request->price,
                'discount_price' => $request->discount_price ?? null,
                'status'      => 'pending',
            ]);

            // Handle cover image upload if available
            if ($request->hasFile('cover_image')) {
                $coverImagePath = $this->handleCoverImage($request->file('cover_image'), $user, $service);
                $service->cover_image = $coverImagePath; // Assuming the `cover_image` column exists in the service model.
                $service->save();
            }

            // Handle additional images upload
            if ($request->hasFile('images')) {
                $this->handleServiceImages($request->file('images'), $user, $service);
            }
            // Eager load images before returning the response
            $service->load('images')->refresh(); // Load associated images
            // Commit the transaction if everything is successful
            DB::commit();
            return $this->successResponse('Service created successfully.', $service);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to store service', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse('Failed to create service. Please try again later.', 500);
        }
    }

    /**
     * Update a service
     */
    public function update(UpdateUserServiceRequest $request, UserService $service): JsonResponse
    {
        DB::beginTransaction(); // Start transaction
        try {
            /** @var \Illuminate\Http\Request $request */
            $user = $request->user();

            // Check if the authenticated user is the owner of the service
            if ($service->seller_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            // Get the fields to update
            $updateFields = $request->only(['title', 'description', 'price', 'discount_price', 'status']);

            // Update slug if title is changed
            if (isset($updateFields['title'])) {
                $updateFields['slug'] = $this->generateUniqueSlug($updateFields['title']);
            }

            // Update the service details
            $service->update($updateFields);

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                // Delete old cover image if it exists
                if ($service->cover_image) {
                    ImageHelper::deleteEcomImage($service->cover_image);
                }
                
                $coverImagePath = $this->handleCoverImage($request->file('cover_image'), $user, $service);
                $service->update(['cover_image' => $coverImagePath]);
            }

            // Handle additional images upload
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                if (!is_array($images)) {
                    $images = [$images];
                }
                $this->handleServiceImages($images, $user, $service);
            }

            // Remove images if any remove_image_ids are provided
            if ($request->filled('remove_image_ids')) {
                $this->removeServiceImages($request->input('remove_image_ids'), $service);
            }

            $service->load(['images'])->refresh(); // This will load the images relation and refresh the model
            // Commit the transaction after all operations are successful
            DB::commit();

            // Return success response
            return $this->successResponse('Service updated successfully.', $service);
        } catch (\Throwable $e) {
            DB::rollBack(); // Rollback in case of error

            \Log::error('Failed to update service', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to update service.', 500);
        }
    }

    /**
     * Show service details by ID or slug
     */
    public function show($serviceIdentifier): JsonResponse
    {
        try {
            // Try to retrieve the service from the cache first (cached for 1 day)
            $cacheKey = "service_{$serviceIdentifier}"; // Cache key based on service identifier
            $service = Cache::remember($cacheKey, now()->addDay(), function () use ($serviceIdentifier) {
                // Try fetching the service by either ID or slug (SKU)
                return UserService::with(['images', 'seller'])
                    ->where('id', $serviceIdentifier) // Try finding by ID
                    ->orWhere('slug', $serviceIdentifier) // Or try finding by slug (SKU)
                    ->first();
            });

            if (!$service) {
                throw new ModelNotFoundException("Service not found");
            }

            return response()->json([
                'success' => true,
                'data' => $service,
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the service wasn't found in both ID and slug queries
            return response()->json([
                'success' => false,
                'message' => 'Service not found. Please check the service ID or SKU.',
            ], 404);
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            \Log::error('Error fetching service details', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
    /**
     * Delete a service
     */
    public function destroy(Request $request, UserService $service): JsonResponse
    {
        $user = $request->user();

        if ($service->seller_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $service->delete();

        return $this->successResponse('Service deleted successfully.');
    }

    /**
     * Generate a unique slug for the service
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
     * Handle the upload and saving of the cover image for the service.
     *
     * @param \Illuminate\Http\UploadedFile $coverImage
     * @param \App\Models\User $user
     * @param \App\Models\Ecommerce\UserService $service
     * @return string $path
     */
    private function handleCoverImage($coverImage, $user, $service): string
    {
        // Upload the image using existing helper
        return ImageHelper::uploadEcomImage($coverImage, $user->id, 'services/cover');
    }

    /**
     * Handle the uploading and saving of service images.
     *
     * @param \Illuminate\Http\UploadedFile[] $images
     * @param \App\Models\User $user
     * @param \App\Models\Ecommerce\UserService $service
     * @return void
     */
    private function handleServiceImages(array $images, $user, $service): void
    {
        foreach ($images as $img) {
            $path = ImageHelper::uploadEcomImage($img, $user->id, 'services/gallery');
            UserServiceImage::create([
                'service_id' => $service->id,
                'image_path' => $path,
            ]);
        }
    }


    /**
     * Remove service images based on the provided image IDs
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
