<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscription\PurchaseSubscriptionRequest;
use App\Models\SubscriptionPlan;
use App\Services\Subscription\SubscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function plans(Request $request): JsonResponse
    {
        try {
            $query = SubscriptionPlan::where('is_active', true)->orderBy('sort_order');

            if ($request->filled('profile_type')) {
                $profileType = $request->profile_type;
                $mappedType = $profileType === 'ecommerce' ? 'seller' : $profileType;
                $query->where('profile_type', $mappedType);
            }

            $plans = $query->get();

            return $this->success([
                'plans' => $plans,
                'filters' => [
                    'profile_type' => $request->profile_type,
                ],
            ], 'Subscription plans retrieved successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to fetch subscription plans', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to fetch subscription plans.');
        }
    }

    public function planDetails(int $id): JsonResponse
    {
        try {
            $plan = SubscriptionPlan::where('is_active', true)->findOrFail($id);

            return $this->success(['plan' => $plan], 'Plan details retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->error('The requested subscription plan was not found.', 404);
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Plan not found.');
        }
    }

    public function purchase(int $profileId, PurchaseSubscriptionRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $profile = $user->profiles()->findOrFail($profileId);

            $subscription = $this->subscriptionService->purchase(
                $user->id,
                $profileId,
                $request->subscription_plan_id,
                $request->payment_method
            );

            if ($request->filled('gateway_subscription_id')) {
                $subscription->update([
                    'gateway_subscription_id' => $request->gateway_subscription_id,
                ]);
            }

            return $this->success([
                'subscription' => $subscription->load('plan'),
            ], 'Subscription purchased successfully.', 201);
        } catch (ModelNotFoundException $e) {
            Log::warning('Subscription purchase failed: Profile or plan not found', [
                'user_id' => $request->user()->id ?? null,
                'profile_id' => $profileId,
                'plan_id' => $request->subscription_plan_id,
                'error' => $e->getMessage()
            ]);
            return $this->error('The requested profile or subscription plan was not found.', 404);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to purchase subscription', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to purchase subscription.');
        }
    }

    public function active(int $profileId, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()->findOrFail($profileId);

            $subscription = $this->subscriptionService->getActiveForProfile($profileId);

            if (!$subscription) {
                return $this->error('No active subscription found for this profile.', 404);
            }

            return $this->success(['subscription' => $subscription], 'Active subscription retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            Log::warning('Failed to fetch active subscription: Profile not found', [
                'user_id' => $request->user()->id ?? null,
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
            return $this->error('The requested profile was not found.', 404);
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch active subscription.');
        }
    }

    public function history(int $profileId, Request $request): JsonResponse
    {
        try {
            $profile = $request->user()->profiles()->findOrFail($profileId);

            $history = $this->subscriptionService->getHistory($profileId);

            return $this->success(['history' => $history], 'Subscription history retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            Log::warning('Failed to fetch subscription history: Profile not found', [
                'user_id' => $request->user()->id ?? null,
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
            return $this->error('The requested profile was not found.', 404);
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch subscription history.');
        }
    }

    public function cancel(int $id, Request $request): JsonResponse
    {
        try {
            $reason = $request->input('reason');

            $subscription = $this->subscriptionService->cancel($id, $request->user()->id, $reason);

            return $this->success([
                'subscription' => $subscription,
            ], 'Subscription cancelled successfully.');
        } catch (ModelNotFoundException $e) {
            Log::warning('Subscription cancellation failed: Subscription not found', [
                'user_id' => $request->user()->id ?? null,
                'subscription_id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->error('The requested subscription was not found.', 404);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to cancel subscription', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to cancel subscription.');
        }
    }

    public function initiate(int $profileId, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'subscription_plan_id' => 'required|integer|exists:subscription_plans,id',
            ]);

            $user = $request->user();
            $profile = $user->profiles()->findOrFail($profileId);
            
            $plan = SubscriptionPlan::where('is_active', true)->findOrFail($request->subscription_plan_id);

            if ($plan->profile_type !== $profile->type) {
                return $this->error("The selected plan is not available for {$profile->type} profiles.", 422);
            }

            // Check if profile already has an active subscription
            $activeSub = $profile->activeSubscription()->exists();
            if ($activeSub) {
                return $this->error('This profile already has an active subscription.', 422);
            }

            // If it is a free plan, no Razorpay initiation needed
            if ($plan->isFree()) {
                return $this->success([
                    'requires_payment' => false,
                    'gateway' => null,
                    'gateway_subscription_id' => null,
                    'razorpay_key' => null,
                ], 'Free subscription plan initiated successfully.');
            }

            // If it is a paid plan, get the config dynamically from settings database table (not .env)
            $gatewayName = app(\App\Services\Setting\SettingService::class)->get('payment.active_gateway', 'razorpay');
            $gatewayConfig = app(\App\Services\Setting\SettingService::class)->getGatewayConfig($gatewayName);
            $publicKey = $gatewayConfig['key'] ?? '';

            if (empty($plan->gateway_plan_id)) {
                return $this->error('The selected plan is not configured in the payment gateway. Please contact support.', 422);
            }

            // Call Gateway to create subscription on Razorpay
            $gateway = app(\App\Services\Payment\GatewayManager::class)->driver($gatewayName);
            
            $result = $gateway->createSubscription(
                $user->gateway_customer_id ?? '',
                $plan->gateway_plan_id
            );

            if (!$result->success) {
                return $this->error($result->message ?? 'Failed to initiate subscription with payment gateway.', 502);
            }

            return $this->success([
                'requires_payment' => true,
                'gateway' => $gatewayName,
                'gateway_subscription_id' => $result->gatewaySubscriptionId,
                'razorpay_key' => $publicKey, // Public Key dynamically fetched from SettingService
                'plan' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => (float) $plan->price,
                    'currency' => $plan->currency,
                ]
            ], 'Subscription initiated successfully.');
        } catch (ModelNotFoundException $e) {
            Log::warning('Subscription initiation failed: Profile not found', [
                'user_id' => $request->user()->id ?? null,
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
            return $this->error('The requested profile was not found.', 404);
        } catch (Throwable $e) {
            Log::error('Failed to initiate subscription', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to initiate subscription.');
        }
    }
}

