<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscription\PurchaseSubscriptionRequest;
use App\Models\SubscriptionPlan;
use App\Services\Subscription\SubscriptionService;
use App\Traits\ApiResponse;
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
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to cancel subscription', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to cancel subscription.');
        }
    }
}
