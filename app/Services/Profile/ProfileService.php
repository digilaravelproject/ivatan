<?php

namespace App\Services\Profile;

use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\Subscription\SubscriptionAssignmentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    public function __construct(
        protected SubscriptionAssignmentService $subscriptionAssignmentService
    ) {}

    public const ALLOWED_TYPES = ['personal', 'employer', 'seller', 'music', 'creator'];

    public const APPROVAL_REQUIRED_TYPES = ['employer', 'seller', 'music', 'creator'];

    public function createPersonalProfile(int $userId): Profile
    {
        return DB::transaction(function () use ($userId) {
            $profile = Profile::create([
                'user_id' => $userId,
                'type' => 'personal',
                'status' => 'active',
                'is_active' => true,
                'is_default' => true,
            ]);

            $this->subscriptionAssignmentService->assignDefaultPlan($profile);

            Log::info("Personal profile created for user: {$userId}", ['profile_id' => $profile->id]);

            return $profile;
        });
    }

    public function createProfile(int $userId, string $type, array $details = []): Profile
    {
        return DB::transaction(function () use ($userId, $type, $details) {
            $profile = Profile::create([
                'user_id' => $userId,
                'type' => $type,
                'status' => in_array($type, self::APPROVAL_REQUIRED_TYPES)
                    ? 'pending_approval'
                    : 'active',
                'is_active' => false,
                'is_default' => false,
            ]);

            $this->createTypeDetails($profile, $details);

            if ($profile->status === 'active') {
                $this->subscriptionAssignmentService->assignDefaultPlan($profile);
            }

            Cache::forget("profile_config:{$userId}");

            Log::info("Profile created for user: {$userId}", [
                'profile_id' => $profile->id,
                'type' => $type,
            ]);

            return $profile->load($this->getDetailRelation($type));
        });
    }

    public function activateProfile(Profile $profile): void
    {
        DB::transaction(function () use ($profile) {
            Profile::where('user_id', $profile->user_id)
                ->where('id', '!=', $profile->id)
                ->update(['is_active' => false]);

            $profile->update([
                'status' => 'active',
                'is_active' => true,
                'approved_at' => now(),
            ]);

            $this->subscriptionAssignmentService->assignDefaultPlan($profile);

            Cache::forget("profile_config:{$profile->user_id}");

            Log::info("Profile activated: {$profile->id}", ['user_id' => $profile->user_id]);
        });
    }

    public function deactivateProfile(Profile $profile): void
    {
        $profile->update(['is_active' => false]);
    }

    public function activateAndDeactivateOld(Profile $newProfile): void
    {
        DB::transaction(function () use ($newProfile) {
            Profile::where('user_id', $newProfile->user_id)
                ->where('is_active', true)
                ->where('id', '!=', $newProfile->id)
                ->update(['is_active' => false]);

            $newProfile->update([
                'status' => 'active',
                'is_active' => true,
                'approved_at' => $newProfile->approved_at ?? now(),
            ]);
        });
    }

    public function deleteProfile(Profile $profile): void
    {
        if ($profile->is_default || $profile->isPersonal()) {
            throw new \DomainException('The default Personal profile cannot be deleted.');
        }

        $profile->delete();

        Cache::forget("profile_config:{$profile->user_id}");
    }

    public function getActiveProfile(int $userId): ?Profile
    {
        return Profile::where('user_id', $userId)
            ->where('is_active', true)
            ->where('status', 'active')
            ->with($this->getDetailRelations())
            ->first();
    }

    public function getDetailRelation(string $type): ?string
    {
        return match ($type) {
            'seller' => 'sellerDetails',
            'employer' => 'employerDetails',
            'music' => 'musicDetails',
            'creator' => 'creatorDetails',
            default => null,
        };
    }

    protected function getDetailRelations(): array
    {
        return ['sellerDetails', 'employerDetails', 'musicDetails', 'creatorDetails'];
    }

    protected function createTypeDetails(Profile $profile, array $details): void
    {
        match ($profile->type) {
            'seller' => $profile->sellerDetails()->create([
                'seller_type' => $details['seller_type'] ?? 'products',
                'business_name' => $details['business_name'] ?? null,
                'business_description' => $details['business_description'] ?? null,
                'business_email' => $details['business_email'] ?? null,
                'business_phone' => $details['business_phone'] ?? null,
                'business_address' => $details['business_address'] ?? null,
            ]),
            'employer' => $profile->employerDetails()->create([
                'company_name' => $details['company_name'] ?? '',
                'industry' => $details['industry'] ?? null,
                'company_size' => $details['company_size'] ?? null,
                'company_website' => $details['company_website'] ?? null,
                'company_phone' => $details['company_phone'] ?? null,
                'company_address' => $details['company_address'] ?? null,
            ]),
            'music' => $profile->musicDetails()->create([
                'artist_name' => $details['artist_name'] ?? null,
                'stage_name' => $details['stage_name'] ?? null,
                'genre' => $details['genre'] ?? null,
                'label' => $details['label'] ?? null,
                'bio' => $details['bio'] ?? null,
            ]),
            'creator' => $profile->creatorDetails()->create([
                'channel_name' => $details['channel_name'] ?? '',
                'content_category' => $details['content_category'] ?? null,
                'platform' => $details['platform'] ?? null,
                'bio' => $details['bio'] ?? null,
            ]),
            default => null,
        };
    }
}
