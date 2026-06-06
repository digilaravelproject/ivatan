<?php

namespace App\Services\Subscription;

use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionAssignmentService
{
    public function assignDefaultPlan(Profile $profile): UserSubscription
    {
        $defaultPlan = SubscriptionPlan::where('profile_type', $profile->type)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (!$defaultPlan) {
            Log::warning("No default plan found for profile type: {$profile->type}", [
                'profile_id' => $profile->id,
            ]);

            $defaultPlan = SubscriptionPlan::create([
                'profile_type' => $profile->type,
                'name' => 'Default Free',
                'slug' => "{$profile->type}-default-free",
                'description' => 'Default free plan',
                'price' => 0,
                'duration_days' => 36500,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 0,
            ]);
        }

        return UserSubscription::create([
            'user_id' => $profile->user_id,
            'profile_id' => $profile->id,
            'subscription_plan_id' => $defaultPlan->id,
            'starts_at' => now(),
            'ends_at' => $defaultPlan->duration_days === 36500 ? null : now()->addDays($defaultPlan->duration_days),
            'status' => 'active',
        ]);
    }

    public function assignOnApproval(int $userId, Profile $newProfile, ?int $oldProfileId = null): UserSubscription
    {
        $existingActiveSub = UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->lockForUpdate()
            ->latest()
            ->first();

        if ($existingActiveSub && $existingActiveSub->plan->profile_type === $newProfile->type) {
            return UserSubscription::create([
                'user_id' => $userId,
                'profile_id' => $newProfile->id,
                'subscription_plan_id' => $existingActiveSub->subscription_plan_id,
                'starts_at' => now(),
                'ends_at' => $existingActiveSub->ends_at,
                'status' => 'active',
            ]);
        }

        return $this->assignDefaultPlan($newProfile);
    }
}
