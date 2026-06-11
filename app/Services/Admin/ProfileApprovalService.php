<?php

namespace App\Services\Admin;

use App\Models\Profile;
use App\Models\ProfileSwitchRequest;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\Profile\ProfileService;
use App\Services\Subscription\SubscriptionAssignmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProfileApprovalService
{
    public function __construct(
        protected ProfileService $profileService,
        protected SubscriptionAssignmentService $subscriptionAssignmentService
    ) {}

    public function approve(ProfileSwitchRequest $switchRequest, int $adminId, ?string $notes = null): array
    {
        return DB::transaction(function () use ($switchRequest, $adminId, $notes) {
            $user = $switchRequest->user;
            $newProfile = $switchRequest->toProfile;

            if (!$newProfile) {
                throw new \RuntimeException('Target profile not found.');
            }

            Profile::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $newProfile->update([
                'status' => 'active',
                'is_active' => true,
                'approved_at' => now(),
            ]);

            $assignedSubscription = $this->subscriptionAssignmentService->assignOnApproval(
                $user->id,
                $newProfile,
                $switchRequest->from_profile_id
            );

            $switchRequest->update([
                'status' => 'approved',
                'approved_by' => $adminId,
                'approved_at' => now(),
                'admin_notes' => $notes,
            ]);

            Log::info("Profile switch approved", [
                'request_id' => $switchRequest->id,
                'user_id' => $user->id,
                'new_profile_id' => $newProfile->id,
                'admin_id' => $adminId,
            ]);

            Cache::forget("profile_config:{$user->id}");

            return [
                'switch_request' => $switchRequest->fresh()->load(['fromProfile', 'toProfile', 'approver']),
                'new_active_profile' => $newProfile->fresh()->load(['activeSubscription.plan']),
                'assigned_subscription' => $assignedSubscription,
            ];
        });
    }

    public function reject(ProfileSwitchRequest $switchRequest, int $adminId, ?string $notes = null): ProfileSwitchRequest
    {
        return DB::transaction(function () use ($switchRequest, $adminId, $notes) {
            $switchRequest->update([
                'status' => 'rejected',
                'approved_by' => $adminId,
                'admin_notes' => $notes,
            ]);

            $targetProfile = $switchRequest->toProfile;
            if ($targetProfile && $targetProfile->status === 'pending_approval') {
                $targetProfile->delete();
            }

            Cache::forget("profile_config:{$switchRequest->user_id}");

            Log::info("Profile switch rejected", [
                'request_id' => $switchRequest->id,
                'user_id' => $switchRequest->user_id,
                'admin_id' => $adminId,
            ]);

            return $switchRequest->fresh()->load(['fromProfile', 'toProfile', 'approver']);
        });
    }

    public function listPendingRequests(array $filters = []): array
    {
        $query = ProfileSwitchRequest::with([
            'user:id,name,email,username',
            'fromProfile',
            'toProfile',
        ])->where('status', 'pending');

        if (!empty($filters['profile_type'])) {
            $query->where('to_profile_type', $filters['profile_type']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 20)->toArray();
    }
}
