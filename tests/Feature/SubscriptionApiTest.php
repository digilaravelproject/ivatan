<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

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

// ─────────────────────────────────────────────
// Subscription Plans (Public)
// ─────────────────────────────────────────────

test('can list all subscription plans', function () {
    $response = $this->getJson('/api/subscription-plans');

    $response->assertOk()
        ->assertJsonStructure([
            'status', 'message', 'data' => ['plans'],
        ]);

    // 10 plans: 5 personal + 3 seller + 2 creator
    $response->assertJsonCount(10, 'data.plans');
});

test('can filter subscription plans by profile type', function () {
    $response = $this->getJson('/api/subscription-plans?profile_type=seller');

    $response->assertOk()
        ->assertJsonCount(3, 'data.plans');
});

test('can view single plan details', function () {
    $plan = SubscriptionPlan::first();

    $response = $this->getJson("/api/subscription-plans/{$plan->id}");

    $response->assertOk()
        ->assertJsonPath('data.plan.id', $plan->id);
});

test('returns 404 for non-existent plan', function () {
    $response = $this->getJson('/api/subscription-plans/99999');

    $response->assertStatus(404);
});

// ─────────────────────────────────────────────
// Purchasing Subscriptions
// ─────────────────────────────────────────────

test('user can purchase free subscription', function () {
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
        ->where('price', 0)
        ->first();

    $response = $this->withToken($token)
        ->postJson("/api/v1/profiles/{$profile->id}/subscriptions", [
            'subscription_plan_id' => $plan->id,
            'payment_method' => 'free',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['subscription' => ['id', 'status', 'plan']]])
        ->assertJsonPath('data.subscription.status', 'active');
});

test('cannot purchase plan from different profile type', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $sellerPlan = SubscriptionPlan::where('profile_type', 'seller')->first();

    $response = $this->withToken($token)
        ->postJson("/api/v1/profiles/{$profile->id}/subscriptions", [
            'subscription_plan_id' => $sellerPlan->id,
            'payment_method' => 'free',
        ]);

    $response->assertStatus(422);
});

test('can view active subscription', function () {
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

    UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => null,
        'status' => 'active',
    ]);

    $response = $this->withToken($token)
        ->getJson("/api/v1/profiles/{$profile->id}/subscriptions/active");

    $response->assertOk()
        ->assertJsonPath('data.subscription.status', 'active');
});

test('can view subscription history', function () {
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

    UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now()->subDays(60),
        'ends_at' => now()->subDays(30),
        'status' => 'expired',
    ]);

    $response = $this->withToken($token)
        ->getJson("/api/v1/profiles/{$profile->id}/subscriptions/history");

    $response->assertOk()
        ->assertJsonCount(1, 'data.history');
});

test('can cancel active subscription', function () {
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
        ->postJson("/api/v1/subscriptions/{$subscription->id}/cancel", [
            'reason' => 'Not needed anymore',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.subscription.status', 'cancelled');
});

test('cannot cancel expired subscription', function () {
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
        'starts_at' => now()->subDays(60),
        'ends_at' => now()->subDays(30),
        'status' => 'expired',
    ]);

    $response = $this->withToken($token)
        ->postJson("/api/v1/subscriptions/{$subscription->id}/cancel");

    $response->assertStatus(422);
});
