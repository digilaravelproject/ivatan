<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure essential roles exist
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'admin']);

    // Seed subscription plans
    if (\Illuminate\Support\Facades\Schema::hasTable('subscription_plans')) {
        \App\Models\SubscriptionPlan::query()->delete();
    }
    $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
});

// ─────────────────────────────────────────────
// Profile Types (Public)
// ─────────────────────────────────────────────

test('can fetch available profile types', function () {
    $response = $this->getJson('/api/profile-types');

    $response->assertOk()
        ->assertJsonStructure([
            'status', 'message', 'data' => ['types' => [
                '*' => ['type', 'label', 'description', 'is_default', 'requires_approval'],
            ]],
        ])
        ->assertJsonCount(5, 'data.types');
});

// ─────────────────────────────────────────────
// Personal Profile Created on Registration
// ─────────────────────────────────────────────

test('personal profile is created on registration', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '03123456789',
        'username' => 'testuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'date_of_birth' => '1995-01-01',
    ]);

    $response->assertStatus(201);

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();

    $profile = $user->profiles()->first();
    expect($profile)->not->toBeNull()
        ->and($profile->type)->toBe('personal')
        ->and($profile->is_active)->toBeTrue()
        ->and($profile->is_default)->toBeTrue()
        ->and($profile->status)->toBe('active');
});

// ─────────────────────────────────────────────
// Profile CRUD
// ─────────────────────────────────────────────

test('can list own profiles', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    // Manually create a personal profile (since registration isn't called)
    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->getJson('/api/v1/profiles');

    $response->assertOk()
        ->assertJsonStructure([
            'status', 'message', 'data' => ['profiles', 'active_profile'],
        ]);
});

test('cannot create duplicate profile type', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    // First employer profile creation should succeed
    $this->withToken($token)
        ->postJson('/api/v1/profiles', [
            'type' => 'employer',
            'company_name' => 'Test Corp',
        ])->assertStatus(201);

    // Second employer profile should be rejected as duplicate
    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles', [
            'type' => 'employer',
            'company_name' => 'Another Corp',
        ]);

    $response->assertStatus(409);
});

test('can create employer profile (requires approval)', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles', [
            'type' => 'employer',
            'company_name' => 'Test Corp',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.profile.status', 'pending_approval')
        ->assertJsonPath('message', 'Profile created successfully. Pending admin approval.');
});

test('can create seller profile', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles', [
            'type' => 'seller',
            'seller_type' => 'products',
            'business_name' => 'My Shop',
        ]);

    $response->assertStatus(201);
});

test('seller creating "both" type without subscription is rejected', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles', [
            'type' => 'seller',
            'seller_type' => 'both',
            'business_name' => 'My Shop',
        ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'A subscription is required to sell both products and services. Please purchase a subscription first.');
});

test('can create content creator profile', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles', [
            'type' => 'creator',
            'channel_name' => 'My Channel',
        ]);

    $response->assertStatus(201);
});

test('cannot delete personal profile', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->deleteJson("/api/v1/profiles/{$profile->id}");

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The default Personal profile cannot be deleted.');
});

// ─────────────────────────────────────────────
// Profile Switching
// ─────────────────────────────────────────────

test('user can request profile switch', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles/switch', [
            'to_profile_type' => 'employer',
            'notes' => 'I want to hire people',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Approval is pending.')
        ->assertJsonStructure(['data' => ['switch_request' => ['id', 'status', 'to_profile_type']]]);
});

test('cannot request switch to same profile type', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/profiles/switch', [
            'to_profile_type' => 'personal',
        ]);

    $response->assertStatus(422);
});

test('cannot create duplicate pending switch request', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $this->withToken($token)->postJson('/api/v1/profiles/switch', [
        'to_profile_type' => 'employer',
    ]);

    $response = $this->withToken($token)->postJson('/api/v1/profiles/switch', [
        'to_profile_type' => 'seller',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'You already have a pending switch request.');
});

test('can view own switch requests', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $personal = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $this->withToken($token)->postJson('/api/v1/profiles/switch', [
        'to_profile_type' => 'employer',
    ]);

    $response = $this->withToken($token)->getJson('/api/v1/profile-switch-requests');

    $response->assertOk()
        ->assertJsonCount(1, 'data.switch_requests');
});

// ─────────────────────────────────────────────
// Seller Details
// ─────────────────────────────────────────────

test('can update seller type', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'seller',
        'status' => 'active',
        'is_active' => true,
        'is_default' => false,
    ]);

    $profile->sellerDetails()->create([
        'seller_type' => 'products',
        'business_name' => 'Shop',
    ]);

    $response = $this->withToken($token)
        ->putJson("/api/v1/profiles/{$profile->id}/seller-details", [
            'seller_type' => 'services',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.seller_details.seller_type', 'services');
});

test('updating seller to "both" without subscription fails', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'seller',
        'status' => 'active',
        'is_active' => true,
        'is_default' => false,
    ]);

    $profile->sellerDetails()->create([
        'seller_type' => 'products',
        'business_name' => 'Shop',
    ]);

    $response = $this->withToken($token)
        ->putJson("/api/v1/profiles/{$profile->id}/seller-details", [
            'seller_type' => 'both',
        ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'A subscription is required to sell both products and services. Please purchase a subscription first.');
});

// ─────────────────────────────────────────────
// Active Profile Endpoint
// ─────────────────────────────────────────────

test('can fetch active profile', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->withToken($token)->getJson('/api/v1/profiles/active');

    $response->assertOk()
        ->assertJsonPath('data.profile.type', 'personal');
});
