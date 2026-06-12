<?php

namespace App\Services\Profile;

use App\Models\Profile;
use App\Models\ProfileSwitchRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileSwitchService
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    public function requestSwitch(int $userId, string $toProfileType, ?string $notes = null, array $details = []): ProfileSwitchRequest
    {
        return DB::transaction(function () use ($userId, $toProfileType, $notes, $details) {
            $activeProfile = Profile::where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('id', 'desc')
                ->first();

            if (!$activeProfile) {
                $activeProfile = Profile::where('user_id', $userId)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($activeProfile) {
                    $activeProfile->update(['is_active' => true]);
                } else {
                    $activeProfile = $this->profileService->createPersonalProfile($userId);
                }
            }

            if ($activeProfile->type === $toProfileType) {
                if ($toProfileType === 'seller' && isset($details['seller_type'])) {
                    $currentSellerType = $activeProfile->sellerDetails?->seller_type;
                    if ($currentSellerType === $details['seller_type']) {
                        throw new \RuntimeException("You are already on the {$toProfileType} profile with subtype {$currentSellerType}.");
                    }
                } else {
                    throw new \RuntimeException("You are already on the {$toProfileType} profile.");
                }
            }

            $existingActive = Profile::where('user_id', $userId)
                ->where('type', $toProfileType)
                ->where('status', 'active')
                ->where('is_active', true)
                ->first();

            if ($existingActive) {
                $isChangingSubtype = ($toProfileType === 'seller' && 
                                      isset($details['seller_type']) && 
                                      $existingActive->sellerDetails?->seller_type !== $details['seller_type']);
                
                if (!$isChangingSubtype) {
                    throw new \RuntimeException("You already have an active {$toProfileType} profile.");
                }
            }

            $existingProfile = Profile::where('user_id', $userId)
                ->where('type', $toProfileType)
                ->withTrashed()
                ->first();

            if ($existingProfile && $existingProfile->trashed()) {
                $existingProfile->restore();
                $existingProfile->update(['status' => 'pending_approval', 'is_active' => false]);
            }

            $targetProfile = $existingProfile;

            if (!$targetProfile) {
                $targetProfile = $this->profileService->createProfile($userId, $toProfileType, $details);
            } else {
                if ($toProfileType === 'seller' && isset($details['seller_type'])) {
                    $sellerDetails = $targetProfile->sellerDetails;
                    if ($sellerDetails) {
                        $sellerDetails->update(['seller_type' => $details['seller_type']]);
                    } else {
                        $targetProfile->sellerDetails()->create(['seller_type' => $details['seller_type']]);
                    }
                }
            }

            $pendingRequest = ProfileSwitchRequest::where('user_id', $userId)
                ->where('status', 'pending')
                ->exists();

            if ($pendingRequest) {
                throw new \RuntimeException('You already have a pending switch request.');
            }

            $switchRequest = ProfileSwitchRequest::create([
                'user_id' => $userId,
                'from_profile_id' => $activeProfile->id,
                'to_profile_id' => $targetProfile->id,
                'to_profile_type' => $toProfileType,
                'status' => 'pending',
                'user_notes' => $notes,
            ]);

            // Log::info("Profile switch requested", [
            //     'user_id' => $userId,
            //     'from' => $activeProfile->type,
            //     'to' => $toProfileType,
            //     'request_id' => $switchRequest->id,
            // ]);

            return $switchRequest->load(['fromProfile', 'toProfile']);
        });
    }

    public function getPendingRequests(int $userId): array
    {
        return ProfileSwitchRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->with(['fromProfile', 'toProfile'])
            ->get()
            ->toArray();
    }

    public function getUserRequests(int $userId): array
    {
        return ProfileSwitchRequest::where('user_id', $userId)
            ->with(['fromProfile', 'toProfile', 'approver'])
            ->latest()
            ->get()
            ->toArray();
    }
}
