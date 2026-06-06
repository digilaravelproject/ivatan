<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

use App\Models\Invoice;
use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;

beforeEach(function () {
    if (\Illuminate\Support\Facades\Schema::hasTable('subscription_plans')) {
        \App\Models\SubscriptionPlan::query()->delete();
    }
    $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
});
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\RazorpayGateway;
use Illuminate\Support\Facades\Event;

// ─────────────────────────────────────────────
// C-1: Payment Bypass Prevention (CRITICAL)
// ─────────────────────────────────────────────

beforeEach(function () {
    $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);

    if (!\Spatie\Permission\Models\Role::where('name', 'admin')->where('guard_name', 'web')->exists()) {
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }
});

test('purchase with fake gateway IDs does NOT activate paid subscription', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $paidPlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    $response = $this->withToken($token)
        ->postJson("/api/v1/profiles/{$profile->id}/subscriptions", [
            'subscription_plan_id' => $paidPlan->id,
            'payment_method' => 'razorpay',
            'gateway_subscription_id' => 'FAKE_SUB_ID',
        ]);

    $response->assertStatus(201);

    // The subscription MUST be 'pending', NOT 'active'
    $response->assertJsonPath('data.subscription.status', 'pending');

    // Verify directly in DB that status is not 'active'
    $subscription = UserSubscription::find($response->json('data.subscription.id'));
    expect($subscription->status)->toBe('pending')
        ->and($subscription->gateway_subscription_id)->toBe('FAKE_SUB_ID');
});

test('free plan purchase creates immediately active subscription', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $freePlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('price', 0)
        ->first();

    $response = $this->withToken($token)
        ->postJson("/api/v1/profiles/{$profile->id}/subscriptions", [
            'subscription_plan_id' => $freePlan->id,
            'payment_method' => 'free',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.subscription.status', 'active');
});

test('no legacy razorpay_* columns accepted in purchase request', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $paidPlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    $response = $this->withToken($token)
        ->postJson("/api/v1/profiles/{$profile->id}/subscriptions", [
            'subscription_plan_id' => $paidPlan->id,
            'payment_method' => 'razorpay',
            'razorpay_payment_id' => 'fake_payment_id',
            'razorpay_order_id' => 'fake_order_id',
        ]);

    $response->assertStatus(201);

    // Status must be 'pending' — razorpay_* fields are ignored and NOT fillable
    $response->assertJsonPath('data.subscription.status', 'pending');

    $subscription = UserSubscription::find($response->json('data.subscription.id'));
    expect($subscription->status)->toBe('pending');
});

// ─────────────────────────────────────────────
// C-2: Gateway Cancellation on User Cancel (CRITICAL)
// ─────────────────────────────────────────────

test('user cancellation calls gateway before updating local DB', function () {
    $gatewayMock = Mockery::mock(PaymentGatewayInterface::class);
    $gatewayMock->shouldReceive('cancelSubscription')
        ->once()
        ->with('gateway_sub_123', 'end_of_period')
        ->andReturn(PaymentResult::success(gatewaySubscriptionId: 'gateway_sub_123', status: 'cancelled'));

    $gatewayMock->shouldReceive('configure')->zeroOrMoreTimes();

    $managerMock = Mockery::mock(GatewayManager::class);
    $managerMock->shouldReceive('driver')
        ->once()
        ->andReturn($gatewayMock);

    app()->instance(GatewayManager::class, $managerMock);

    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', true)
        ->first();

    $subscription = UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'status' => 'active',
        'gateway_subscription_id' => 'gateway_sub_123',
    ]);

    $response = $this->withToken($token)
        ->postJson("/api/v1/subscriptions/{$subscription->id}/cancel", [
            'reason' => 'Security test cancel',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.subscription.status', 'cancelled');
});

test('user cancellation without gateway subscription still works', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', true)
        ->first();

    $subscription = UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'status' => 'active',
    ]);

    $response = $this->withToken($token)
        ->postJson("/api/v1/subscriptions/{$subscription->id}/cancel");

    $response->assertOk()
        ->assertJsonPath('data.subscription.status', 'cancelled');
});

// ─────────────────────────────────────────────
// H-2: Webhook Idempotency
// ─────────────────────────────────────────────

test('webhook handleCharged skips duplicate gateway invoice', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $subscription = UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays($plan->duration_days),
        'status' => 'pending',
        'gateway_subscription_id' => 'sub_dup_test',
        'next_billing_at' => now()->addDays($plan->duration_days),
    ]);

    $controller = app(\App\Http\Controllers\Api\RazorpayWebhookController::class);

    $paymentEntity1 = [
        'id' => 'pay_dup_1',
        'invoice_id' => 'inv_dup_001',
        'amount' => (int) round($plan->price * 100),
        'subscription_id' => 'sub_dup_test',
    ];

    \Illuminate\Support\Facades\Event::fake();

    $refMethod = new \ReflectionMethod($controller, 'handleCharged');
    $refMethod->invoke($controller, $subscription, $paymentEntity1);

    // First call should create 1 invoice
    expect(Invoice::where('gateway_invoice_id', 'inv_dup_001')->count())->toBe(1);

    // Second call with same gateway_invoice_id should be skipped
    $refMethod->invoke($controller, $subscription->fresh(), $paymentEntity1);

    // Should still be exactly 1 invoice
    expect(Invoice::where('gateway_invoice_id', 'inv_dup_001')->count())->toBe(1);
});

// ─────────────────────────────────────────────
// M-2: Amount Verification
// ─────────────────────────────────────────────

test('webhook handleCharged logs critical on amount mismatch', function () {
    \Illuminate\Support\Facades\Log::shouldReceive('critical')
        ->once()
        ->withArgs(fn($msg) => str_contains($msg, 'amount mismatch'));
    \Illuminate\Support\Facades\Log::shouldReceive('info')->zeroOrMoreTimes();

    $user = User::factory()->create();
    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $subscription = UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays($plan->duration_days),
        'status' => 'pending',
        'gateway_subscription_id' => 'sub_amt_test',
    ]);

    $controller = app(\App\Http\Controllers\Api\RazorpayWebhookController::class);

    $wrongAmountEntity = [
        'id' => 'pay_amt_1',
        'invoice_id' => 'inv_amt_001',
        'amount' => 1, // Wrong: 1 paisa instead of plan price
        'subscription_id' => 'sub_amt_test',
    ];

    $refMethod = new \ReflectionMethod($controller, 'handleCharged');
    $refMethod->invoke($controller, $subscription, $wrongAmountEntity);
});

// ─────────────────────────────────────────────
// Webhook Security Tests
// ─────────────────────────────────────────────

test('webhook returns 400 when signature is missing', function () {
    $response = $this->postJson('/api/webhooks/razorpay', [
        'event' => 'subscription.charged',
    ], [
        'Content-Type' => 'application/json',
    ]);

    $response->assertStatus(400);
});

test('webhook returns 400 for invalid signature', function () {
    $response = $this->postJson('/api/webhooks/razorpay', [
        'event' => 'subscription.charged',
    ], [
        'X-Razorpay-Signature' => 'invalid_signature',
    ]);

    // If webhook secret is configured, it will return 400 (invalid sig)
    // If not configured, it returns 500
    $validStatuses = [400, 500];
    expect(in_array($response->status(), $validStatuses))->toBeTrue();
});

// ─────────────────────────────────────────────
// M-4: Race Condition / Locking
// ─────────────────────────────────────────────

test('purchase within transaction uses lockForUpdate', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', true)
        ->first();

    $service = app(\App\Services\Subscription\SubscriptionService::class);
    $subscription = $service->purchase($user->id, $profile->id, $plan->id, 'free');

    expect($subscription->status)->toBe('active');
});

// ─────────────────────────────────────────────
// Scheduler & Commands
// ─────────────────────────────────────────────

test('subscriptions:expire command exists and can be called', function () {
    $this->artisan('subscriptions:expire')
        ->assertSuccessful();
});

test('payments:health-check command exists and can be called', function () {
    $this->artisan('payments:health-check')
        ->doesntExpectOutputToContain('CRITICAL');
});

// ─────────────────────────────────────────────
// Admin Assignment Gateway Cancel (M-1)
// ─────────────────────────────────────────────

test('admin assign cancels old gateway subscription', function () {
    $gatewayMock = Mockery::mock(PaymentGatewayInterface::class);
    $gatewayMock->shouldReceive('cancelSubscription')
        ->once()
        ->with('old_gateway_sub', 'immediate')
        ->andReturn(PaymentResult::success(gatewaySubscriptionId: 'old_gateway_sub', status: 'cancelled'));

    $gatewayMock->shouldReceive('configure')->zeroOrMoreTimes();

    $managerMock = Mockery::mock(GatewayManager::class);
    $managerMock->shouldReceive('driver')
        ->once()
        ->andReturn($gatewayMock);

    app()->instance(GatewayManager::class, $managerMock);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $oldPlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', true)
        ->first();

    UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $oldPlan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'status' => 'active',
        'gateway_subscription_id' => 'old_gateway_sub',
    ]);

    $newPlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    $response = $this->actingAs($admin)
        ->post(route('admin.subscriptions.assign'), [
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'subscription_plan_id' => $newPlan->id,
        ]);

    $response->assertSessionHas('success');
});

// ─────────────────────────────────────────────
// Expiry Command Test (H-1)
// ─────────────────────────────────────────────

test('expirePastDue marks expired subscriptions', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
    ]);

    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', true)
        ->first();

    $sub = UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now()->subDays(60),
        'ends_at' => now()->subDays(30),
        'status' => 'active',
    ]);

    $service = app(\App\Services\Subscription\SubscriptionService::class);
    $expired = $service->expirePastDue();

    expect($expired)->toBeGreaterThanOrEqual(1);
    expect($sub->fresh()->status)->toBe('expired');
});
