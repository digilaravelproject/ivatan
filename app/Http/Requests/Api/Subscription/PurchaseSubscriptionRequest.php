<?php

namespace App\Http\Requests\Api\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_plan_id' => 'required|integer|exists:subscription_plans,id',
            'payment_method' => 'nullable|string|in:razorpay,free',
            'gateway_subscription_id' => 'nullable|string|unique:user_subscriptions,gateway_subscription_id',
        ];
    }
}
