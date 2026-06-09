<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\Payment\GatewayManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminSubscriptionPlanController extends Controller
{
    public function __construct(
        protected GatewayManager $gatewayManager
    ) {}

    public function index(): View
    {
        $plans = SubscriptionPlan::orderBy('sort_order')
            ->orderBy('profile_type')
            ->get()
            ->groupBy('profile_type');

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $profileTypes = [
            'personal' => 'Personal',
            'seller' => 'Product & Service Seller',
            'creator' => 'Content Creator',
        ];

        return view('admin.subscription-plans.create', compact('profileTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'profile_type' => 'required|string|in:personal,seller,creator',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['features'] = $validated['features'] ?? [];

        if ($validated['price'] > 0) {
            try {
                $gateway = $this->gatewayManager->driver();
                $result = $gateway->createSubscriptionPlan(
                    $validated['name'],
                    $validated['price'],
                    $validated['currency'],
                    $this->intervalForDuration($validated['duration_days']),
                    $validated['duration_days']
                );

                if ($result->success && $result->transactionId) {
                    $validated['gateway_plan_id'] = $result->transactionId;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to create gateway plan', ['error' => $e->getMessage()]);
            }
        }

        SubscriptionPlan::create($validated);

        $message = 'Subscription plan created successfully.';
        if ($validated['price'] > 0 && empty($validated['gateway_plan_id'])) {
            $message .= ' Warning: Gateway plan could not be created automatically. Set up the plan manually in Razorpay.';
        }

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', $message);
    }

    public function edit(int $id): View
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $profileTypes = [
            'personal' => 'Personal',
            'seller' => 'Product & Service Seller',
            'creator' => 'Content Creator',
        ];

        return view('admin.subscription-plans.edit', compact('plan', 'profileTypes'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'profile_type' => 'required|string|in:personal,seller,creator',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $id,
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['features'] = $validated['features'] ?? [];

        // If the price is > 0 and the plan doesn't have a gateway_plan_id yet, auto-create it
        if ($validated['price'] > 0 && empty($plan->gateway_plan_id)) {
            try {
                $gateway = $this->gatewayManager->driver();
                $result = $gateway->createSubscriptionPlan(
                    $validated['name'],
                    $validated['price'],
                    $validated['currency'],
                    $this->intervalForDuration($validated['duration_days']),
                    $validated['duration_days']
                );

                if ($result->success && $result->transactionId) {
                    $validated['gateway_plan_id'] = $result->transactionId;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to update gateway plan', ['error' => $e->getMessage()]);
            }
        }

        $plan->update($validated);

        $message = 'Subscription plan updated successfully.';
        if ($validated['price'] > 0 && !$plan->gateway_plan_id && empty($validated['gateway_plan_id'])) {
            $message .= ' Warning: Gateway plan could not be synced. Update manually in Razorpay.';
        }

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', $message);
    }

    public function destroy(int $id): RedirectResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        if ($plan->subscriptions()->exists()) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete plan with active or past subscriptions. Deactivate it instead.']);
        }

        $plan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    protected function intervalForDuration(int $durationDays): string
    {
        return match (true) {
            $durationDays >= 365 => 'yearly',
            $durationDays >= 90  => 'monthly',
            $durationDays >= 30  => 'monthly',
            $durationDays >= 7   => 'weekly',
            default              => 'daily',
        };
    }
}
