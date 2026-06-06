<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\CreateProfileRequest;
use App\Http\Requests\Api\Profile\SwitchProfileRequest;
use App\Http\Requests\Api\Profile\UpdateSellerDetailsRequest;
use App\Http\Resources\Profile\ProfileConfigResource;
use App\Services\Profile\ProfileConfigService;
use App\Services\Profile\ProfileService;
use App\Services\Profile\ProfileSwitchService;
use App\Services\Profile\SellerProfileService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProfileService $profileService,
        protected ProfileSwitchService $profileSwitchService,
        protected SellerProfileService $sellerProfileService,
        protected ProfileConfigService $profileConfigService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $profiles = $request->user()->profiles()
                ->with(['sellerDetails', 'employerDetails', 'musicDetails', 'creatorDetails', 'activeSubscription.plan'])
                ->orderBy('is_active', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->success([
                'profiles' => $profiles,
                'active_profile' => $profiles->firstWhere('is_active', true),
            ], 'Profiles retrieved successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to fetch profiles', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to fetch profiles.');
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()
                ->with(['sellerDetails', 'employerDetails', 'musicDetails', 'creatorDetails', 'activeSubscription.plan', 'subscriptions.plan'])
                ->findOrFail($id);

            return $this->success(['profile' => $profile], 'Profile retrieved successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to fetch profile', ['error' => $e->getMessage(), 'profile_id' => $id]);
            return $this->exceptionResponse($e, 'Profile not found.');
        }
    }

    public function active(Request $request): JsonResponse
    {
        try {
            $profile = $this->profileService->getActiveProfile($request->user()->id);

            if (!$profile) {
                return $this->error('No active profile found.', 404);
            }

            return $this->success(['profile' => $profile], 'Active profile retrieved successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to fetch active profile', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to fetch active profile.');
        }
    }

    public function store(CreateProfileRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $existingProfile = $user->profiles()
                ->where('type', $request->type)
                ->exists();

            if ($existingProfile) {
                return $this->error("You already have a {$request->type} profile.", 409);
            }

            if ($request->type === 'seller' && $request->seller_type === 'both') {
                $hasActiveSub = $user->activeSubscriptions()->exists();
                if (!$hasActiveSub) {
                    return $this->error(
                        'A subscription is required to sell both products and services. Please purchase a subscription first.',
                        422
                    );
                }
            }

            $profile = $this->profileService->createProfile(
                $user->id,
                $request->type,
                $request->all()
            );

            $message = in_array($request->type, ProfileService::APPROVAL_REQUIRED_TYPES)
                ? 'Profile created successfully. Pending admin approval.'
                : 'Profile created successfully.';

            return $this->success(['profile' => $profile], $message, 201);
        } catch (Throwable $e) {
            Log::error('Failed to create profile', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to create profile.');
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()->findOrFail($id);

            $this->profileService->deleteProfile($profile);

            return $this->success([], 'Profile deleted successfully.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to delete profile', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to delete profile.');
        }
    }

    public function switchProfile(SwitchProfileRequest $request): JsonResponse
    {
        try {
            $switchRequest = $this->profileSwitchService->requestSwitch(
                $request->user()->id,
                $request->to_profile_type,
                $request->notes
            );

            return $this->success([
                'switch_request' => $switchRequest,
            ], 'Approval is pending.', 201);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to request profile switch', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to request profile switch.');
        }
    }

    public function switchRequests(Request $request): JsonResponse
    {
        try {
            $requests = $this->profileSwitchService->getUserRequests($request->user()->id);

            return $this->success(['switch_requests' => $requests], 'Switch requests retrieved successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to fetch switch requests', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to fetch switch requests.');
        }
    }

    public function sellerDetails(int $id, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()
                ->where('type', 'seller')
                ->findOrFail($id);

            return $this->success([
                'profile_id' => $profile->id,
                'seller_details' => $profile->sellerDetails,
            ], 'Seller details retrieved successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Seller details not found.');
        }
    }

    public function updateSellerDetails(int $id, UpdateSellerDetailsRequest $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()
                ->where('type', 'seller')
                ->findOrFail($id);

            $sellerDetail = $profile->sellerDetails;

            if (!$sellerDetail) {
                return $this->error('Seller details not found. Create a seller profile first.', 404);
            }

            if ($request->has('seller_type')) {
                $this->sellerProfileService->validateSellerTypeChange(
                    $sellerDetail,
                    $request->seller_type
                );
            }

            $sellerDetail->update($request->validated());

            return $this->success([
                'profile_id' => $profile->id,
                'seller_details' => $sellerDetail->fresh(),
            ], 'Seller details updated successfully.');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to update seller details', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to update seller details.');
        }
    }

    public function employerDetails(int $id, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()
                ->where('type', 'employer')
                ->findOrFail($id);

            return $this->success([
                'profile_id' => $profile->id,
                'employer_details' => $profile->employerDetails,
            ], 'Employer details retrieved successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Employer details not found.');
        }
    }

    public function musicDetails(int $id, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()
                ->where('type', 'music')
                ->findOrFail($id);

            return $this->success([
                'profile_id' => $profile->id,
                'music_details' => $profile->musicDetails,
            ], 'Music profile details retrieved successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Music profile details not found.');
        }
    }

    public function creatorDetails(int $id, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()
                ->where('type', 'creator')
                ->findOrFail($id);

            return $this->success([
                'profile_id' => $profile->id,
                'creator_details' => $profile->creatorDetails,
            ], 'Creator details retrieved successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Creator details not found.');
        }
    }

    public function availableTypes(): JsonResponse
    {
        try {
            $types = [
                [
                    'type' => 'personal',
                    'label' => 'Personal Profile',
                    'description' => 'Default personal profile with basic features.',
                    'is_default' => true,
                    'requires_approval' => false,
                    'has_subscription' => true,
                ],
                [
                    'type' => 'employer',
                    'label' => 'Employer Profile',
                    'description' => 'Post job openings and manage recruitment.',
                    'is_default' => false,
                    'requires_approval' => true,
                    'has_subscription' => false,
                ],
                [
                    'type' => 'seller',
                    'label' => 'Product & Service Seller',
                    'description' => 'Sell products, services, or both.',
                    'is_default' => false,
                    'requires_approval' => true,
                    'has_subscription' => true,
                    'seller_types' => ['products', 'services', 'both'],
                ],
                [
                    'type' => 'music',
                    'label' => 'Music Playlist Profile',
                    'description' => 'Create and manage music playlists.',
                    'is_default' => false,
                    'requires_approval' => true,
                    'has_subscription' => false,
                ],
                [
                    'type' => 'creator',
                    'label' => 'Content Creator Profile',
                    'description' => 'Upload content, manage monetization.',
                    'is_default' => false,
                    'requires_approval' => true,
                    'has_subscription' => true,
                ],
            ];

            return $this->success(['types' => $types], 'Profile types retrieved successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch profile types.');
        }
    }

    public function config(Request $request): JsonResponse
    {
        try {
            $data = $this->profileConfigService->getConfig($request->user());

            return $this->success(
                new ProfileConfigResource($data),
                'Profile configuration retrieved successfully.'
            );
        } catch (Throwable $e) {
            Log::error('Failed to fetch profile config', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to fetch profile configuration.');
        }
    }
}
