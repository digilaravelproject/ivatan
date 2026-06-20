<?php

namespace App\Services\Subscription;

use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Payment\GatewayManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct(
        protected GatewayManager $gatewayManager
    ) {}

    public function purchase(int $userId, int $profileId, int $planId, ?string $paymentMethod = null): UserSubscription
    {
        return DB::transaction(function () use ($userId, $profileId, $planId, $paymentMethod) {
            $profile = Profile::where('user_id', $userId)
                ->findOrFail($profileId);

            $plan = SubscriptionPlan::where('is_active', true)->findOrFail($planId);

            if ($plan->profile_type !== $profile->type) {
                throw new \InvalidArgumentException(
                    "The selected plan is not available for {$profile->type} profiles."
                );
            }

            $activeSub = UserSubscription::where('profile_id', $profileId)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->with('plan')
                ->lockForUpdate()
                ->get()
                ->filter(fn($sub) => $sub->plan && !$sub->plan->isFree())
                ->isNotEmpty();

            if ($activeSub) {
                throw new \RuntimeException('This profile already has an active subscription.');
            }

            $subscription = UserSubscription::create([
                'user_id' => $userId,
                'profile_id' => $profileId,
                'subscription_plan_id' => $planId,
                'starts_at' => now(),
                'ends_at' => $plan->duration_days === 36500 ? null : now()->addDays($plan->duration_days),
                'status' => $plan->isFree() ? 'active' : 'pending',
            ]);

            Cache::forget("profile_config:{$userId}");

            Log::info("Subscription purchased", [
                'user_id' => $userId,
                'profile_id' => $profileId,
                'plan_id' => $planId,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription->load('plan');
        });
    }

    public function cancel(int $subscriptionId, int $userId, ?string $reason = null, string $mode = 'end_of_period'): UserSubscription
    {
        return DB::transaction(function () use ($subscriptionId, $userId, $reason, $mode) {
            $subscription = UserSubscription::where('user_id', $userId)
                ->lockForUpdate()
                ->findOrFail($subscriptionId);

            if ($subscription->status !== 'active') {
                throw new \RuntimeException('Only active subscriptions can be cancelled.');
            }

            if ($subscription->gateway_subscription_id) {
                try {
                    $gateway = $this->gatewayManager->driver();
                    $result = $gateway->cancelSubscription(
                        $subscription->gateway_subscription_id,
                        $mode
                    );

                    if (!$result->success) {
                        throw new \RuntimeException('Gateway cancellation failed: ' . $result->message);
                    }
                } catch (PaymentGatewayException $e) {
                    Log::error('Subscription cancel: gateway error', [
                        'subscription_id' => $subscriptionId,
                        'error' => $e->getMessage(),
                    ]);
                    throw new \RuntimeException('Payment gateway error: ' . $e->getMessage());
                }
            }

            $subscription->cancel($reason, $mode);

            Cache::forget("profile_config:{$userId}");

            Log::info("Subscription cancelled", [
                'subscription_id' => $subscriptionId,
                'user_id' => $userId,
                'reason' => $reason,
                'mode' => $mode,
            ]);

            return $subscription->fresh()->load('plan');
        });
    }

    public function getActiveForProfile(int $profileId): ?UserSubscription
    {
        return UserSubscription::where('profile_id', $profileId)
            ->whereIn('status', ['active', 'past_due'])
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->latest()
            ->first();
    }

    public function getHistory(int $profileId): array
    {
        return UserSubscription::where('profile_id', $profileId)
            ->with('plan')
            ->latest()
            ->get()
            ->toArray();
    }

    public function expirePastDue(): int
    {
        $count = 0;

        UserSubscription::where('ends_at', '<', now())
            ->whereIn('status', ['active', 'past_due'])
            ->chunkById(100, function ($subscriptions) use (&$count) {
                foreach ($subscriptions as $subscription) {
                    $subscription->markAsExpired();
                    $count++;
                }
            });

        return $count;
    }
}
