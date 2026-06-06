<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminSubscriptionController extends Controller
{
    public function __construct(
        protected GatewayManager $gatewayManager
    ) {}

    public function index(Request $request): View
    {
        $query = UserSubscription::with(['user:id,name,email,username', 'plan', 'profile']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $subscriptions = $query->latest()->paginate(20)->withQueryString();

        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();

        $summary = [
            'total' => UserSubscription::count(),
            'active' => UserSubscription::whereIn('status', ['active', 'past_due'])->count(),
            'cancelled' => UserSubscription::where('status', 'cancelled')->count(),
            'expired' => UserSubscription::where('status', 'expired')->count(),
            'monthly_revenue' => UserSubscription::whereIn('status', ['active', 'past_due'])
                ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                ->sum('subscription_plans.price') ?? 0,
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'summary'));
    }

    public function show(int $id): View
    {
        $subscription = UserSubscription::with([
            'user:id,name,email,username,phone',
            'plan',
            'profile',
            'invoices' => fn($q) => $q->latest(),
        ])->findOrFail($id);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:2000',
            'mode' => 'required|string|in:end_of_period,immediate',
        ]);

        $subscription = UserSubscription::findOrFail($id);

        if (!$subscription->isActive()) {
            return redirect()->back()->withErrors(['error' => 'Only active subscriptions can be cancelled.']);
        }

        if ($subscription->gateway_subscription_id) {
            try {
                $gateway = $this->gatewayManager->driver();
                $result = $gateway->cancelSubscription(
                    $subscription->gateway_subscription_id,
                    $request->mode
                );

                if (!$result->success) {
                    return redirect()->back()->withErrors(['error' => 'Gateway cancellation failed: ' . $result->message]);
                }
            } catch (PaymentGatewayException $e) {
                Log::error('Admin subscription cancel: gateway error', [
                    'subscription_id' => $id,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->back()->withErrors(['error' => 'Payment gateway error: ' . $e->getMessage()]);
            }
        }

        $subscription->cancel($request->reason, $request->mode);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription cancelled successfully. Gateway notified to stop future charges.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'profile_id' => 'required|integer|exists:profiles,id',
            'subscription_plan_id' => 'required|integer|exists:subscription_plans,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $profile = Profile::findOrFail($request->profile_id);
        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);

        $activeSub = UserSubscription::where('profile_id', $profile->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->first();

        if ($activeSub) {
            if ($activeSub->gateway_subscription_id) {
                try {
                    $gateway = $this->gatewayManager->driver();
                    $result = $gateway->cancelSubscription(
                        $activeSub->gateway_subscription_id,
                        'immediate'
                    );

                    if (!$result->success) {
                        Log::warning('Admin assign: gateway cancel failed', [
                            'subscription_id' => $activeSub->id,
                            'message' => $result->message,
                        ]);
                    }
                } catch (PaymentGatewayException $e) {
                    Log::error('Admin assign: gateway error', [
                        'subscription_id' => $activeSub->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $activeSub->cancel('Replaced by admin assignment', 'immediate');
        }

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => $plan->duration_days === 36500 ? null : now()->addDays($plan->duration_days),
            'status' => 'active',
            'next_billing_at' => $plan->duration_days === 36500 ? null : now()->addDays($plan->duration_days),
            'auto_renew' => $plan->isPaid(),
        ]);

        Log::info('Admin manually assigned subscription', [
            'admin_id' => auth()->id(),
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription->id,
        ]);

        return redirect()->route('admin.subscriptions.show', $subscription->id)
            ->with('success', "Subscription '{$plan->name}' assigned to {$user->name} successfully.");
    }

    public function userSubscriptions(int $userId): View
    {
        $user = User::with([
            'subscriptions' => fn($q) => $q->with('plan', 'profile', 'invoices')->latest(),
        ])->findOrFail($userId);

        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $profiles = Profile::where('user_id', $userId)->get();

        return view('admin.subscriptions.user', compact('user', 'plans', 'profiles'));
    }
}
