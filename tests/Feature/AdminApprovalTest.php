<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

use App\Models\Profile;
use App\Models\ProfileSwitchRequest;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'admin']);

    if (\Illuminate\Support\Facades\Schema::hasTable('subscription_plans')) {
        \App\Models\SubscriptionPlan::query()->delete();
    }
    $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
});

// ─────────────────────────────────────────────
// Admin Approval Flow
// ─────────────────────────────────────────────

test('admin can list pending switch requests', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $adminToken = $admin->createToken('test')->plainTextToken;

    $user = User::factory()->create();
    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    // Create a pending switch request
    $targetProfile = Profile::create([
        'user_id' => $user->id,
        'type' => 'employer',
        'status' => 'pending_approval',
        'is_active' => false,
    ]);

    ProfileSwitchRequest::create([
        'user_id' => $user->id,
        'from_profile_id' => $personal->id,
        'to_profile_id' => $targetProfile->id,
        'to_profile_type' => 'employer',
        'status' => 'pending',
    ]);

    $response = $this->withToken($adminToken)
        ->getJson('/api/v1/admin/profile-switch-requests');

    $response->assertOk();
});

test('non-admin cannot access admin endpoints', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)
        ->getJson('/api/v1/admin/profile-switch-requests');

    $response->assertStatus(403);
});

test('admin can approve switch request and assign default plan', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $adminToken = $admin->createToken('test')->plainTextToken;

    $user = User::factory()->create();
    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $employerProfile = Profile::create([
        'user_id' => $user->id,
        'type' => 'employer',
        'status' => 'pending_approval',
        'is_active' => false,
    ]);

    $switchRequest = ProfileSwitchRequest::create([
        'user_id' => $user->id,
        'from_profile_id' => $personal->id,
        'to_profile_id' => $employerProfile->id,
        'to_profile_type' => 'employer',
        'status' => 'pending',
    ]);

    $response = $this->withToken($adminToken)
        ->postJson("/api/v1/admin/profile-switch-requests/{$switchRequest->id}/approve", [
            'status' => 'approved',
            'admin_notes' => 'Looks good, approved.',
        ]);

    $response->assertOk();

    $employerProfile->refresh();
    $personal->refresh();

    expect($employerProfile->status)->toBe('active')
        ->and($employerProfile->is_active)->toBeTrue()
        ->and($personal->is_active)->toBeFalse()
        ->and($switchRequest->fresh()->status)->toBe('approved');
});

test('admin can reject switch request', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $adminToken = $admin->createToken('test')->plainTextToken;

    $user = User::factory()->create();
    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $employerProfile = Profile::create([
        'user_id' => $user->id,
        'type' => 'employer',
        'status' => 'pending_approval',
        'is_active' => false,
    ]);

    $switchRequest = ProfileSwitchRequest::create([
        'user_id' => $user->id,
        'from_profile_id' => $personal->id,
        'to_profile_id' => $employerProfile->id,
        'to_profile_type' => 'employer',
        'status' => 'pending',
    ]);

    $response = $this->withToken($adminToken)
        ->postJson("/api/v1/admin/profile-switch-requests/{$switchRequest->id}/approve", [
            'status' => 'rejected',
            'admin_notes' => 'Insufficient documentation.',
        ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Profile switch request rejected.');

    expect($switchRequest->fresh()->status)->toBe('rejected');
});

test('approving with active subscription carries over compatible plan', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $adminToken = $admin->createToken('test')->plainTextToken;

    $user = User::factory()->create();

    // Create personal profile with an active subscription
    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $personalPlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $personal->id,
        'subscription_plan_id' => $personalPlan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'status' => 'active',
    ]);

    // Create another personal profile (same type - compatible)
    $newProfile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'pending_approval',
        'is_active' => false,
    ]);

    $switchRequest = ProfileSwitchRequest::create([
        'user_id' => $user->id,
        'from_profile_id' => $personal->id,
        'to_profile_id' => $newProfile->id,
        'to_profile_type' => 'personal',
        'status' => 'pending',
    ]);

    $response = $this->withToken($adminToken)
        ->postJson("/api/v1/admin/profile-switch-requests/{$switchRequest->id}/approve", [
            'status' => 'approved',
        ]);

    $response->assertOk();

    // Should have carried over the same plan
    $newSub = UserSubscription::where('profile_id', $newProfile->id)
        ->where('status', 'active')
        ->first();

    expect($newSub)->not->toBeNull()
        ->and($newSub->subscription_plan_id)->toBe($personalPlan->id);
});

// ─────────────────────────────────────────────
// Model Unit Tests
// ─────────────────────────────────────────────

test('profile helper methods work correctly', function () {
    $user = User::factory()->create();

    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    expect($personal->isPersonal())->toBeTrue()
        ->and($personal->canBeActivated())->toBeTrue()
        ->and($personal->isActiveProfile())->toBeTrue()
        ->and($personal->isPending())->toBeFalse();

    $seller = Profile::create([
        'user_id' => $user->id,
        'type' => 'seller',
        'status' => 'pending_approval',
        'is_active' => false,
    ]);

    expect($seller->isPersonal())->toBeFalse()
        ->and($seller->isPending())->toBeTrue()
        ->and($seller->canBeActivated())->toBeFalse();
});

test('seller detail helper methods work', function () {
    $user = User::factory()->create();

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'seller',
        'status' => 'active',
        'is_active' => true,
    ]);

    $details = $profile->sellerDetails()->create([
        'seller_type' => 'both',
        'business_name' => 'Test Shop',
    ]);

    expect($details->sellsProducts())->toBeTrue()
        ->and($details->sellsServices())->toBeTrue()
        ->and($details->sellsBoth())->toBeTrue();

    $details->update(['seller_type' => 'products']);

    expect($details->fresh()->sellsProducts())->toBeTrue()
        ->and($details->fresh()->sellsServices())->toBeFalse()
        ->and($details->fresh()->sellsBoth())->toBeFalse();
});

test('subscription helper methods work', function () {
    $plan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', true)
        ->first();

    expect($plan->isFree())->toBeTrue()
        ->and($plan->isPaid())->toBeFalse();

    $paidPlan = SubscriptionPlan::where('profile_type', 'personal')
        ->where('is_default', false)
        ->where('price', '>', 0)
        ->first();

    expect($paidPlan->isFree())->toBeFalse()
        ->and($paidPlan->isPaid())->toBeTrue();
});
