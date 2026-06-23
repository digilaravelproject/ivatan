<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\StoreUserServiceRequest;
use App\Http\Requests\Ecommerce\UpdateUserServiceRequest;
use App\Http\Resources\Ecommerce\ServiceResource;
use App\Models\Ecommerce\UserService;
use App\Models\User;
use App\Services\Ecommerce\SellerServiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserServiceController extends Controller
{
    public function __construct(
        protected SellerServiceService $sellerServiceService
    ) {}

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

            $services = $this->sellerServiceService->getSellerServices($user->id);

            return $this->successResponse('Services fetched successfully.', $services);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch seller services', [
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

            $services = $this->sellerServiceService->getSellerServices($seller->id);

            return $this->successResponse('Seller services fetched successfully.', $services);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch services for seller', [
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
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }

            $coverImage = $request->file('cover_image');
            $images = $request->file('images', []);
            if (!is_array($images) && $request->hasFile('images')) {
                $images = [$images];
            }

            $service = $this->sellerServiceService->createService(
                $user,
                $request->only(['title', 'description', 'price', 'discount_price']),
                $coverImage,
                $images
            );

            return $this->successResponse('Service created successfully.', $service);
        } catch (\Throwable $e) {
            Log::error('Failed to store service', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse('Failed to create service. Please try again later.', 500);
        }
    }

    /**
     * Update a service
     */
    public function update(UpdateUserServiceRequest $request, UserService $service): JsonResponse
    {
        try {
            $user = $request->user();

            // Check ownership
            if ($service->seller_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $coverImage = $request->file('cover_image');
            $images = $request->file('images', []);
            if (!is_array($images) && $request->hasFile('images')) {
                $images = [$images];
            }

            $removeImageIds = $request->input('remove_image_ids', []);

            $updatedService = $this->sellerServiceService->updateService(
                $user,
                $service,
                $request->only(['title', 'description', 'price', 'discount_price', 'status']),
                $coverImage,
                $images,
                $removeImageIds
            );

            return $this->successResponse('Service updated successfully.', $updatedService);
        } catch (\Throwable $e) {
            Log::error('Failed to update service', [
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
            $service = $this->sellerServiceService->findService($serviceIdentifier);

            return response()->json([
                'success' => true,
                'data' => new ServiceResource($service),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found. Please check the service ID or SKU.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error fetching service details', [
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
        try {
            $user = $request->user();

            if ($service->seller_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $this->sellerServiceService->deleteService($user, $service);

            return $this->successResponse('Service deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete service', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to delete service.', 500);
        }
    }
}
