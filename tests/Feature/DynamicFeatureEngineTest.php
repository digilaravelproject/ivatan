<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

use App\Models\User;
use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Feature;
use App\Models\Jobs\UserJobPost;
use App\Models\Jobs\UserJobApplication;
use App\Models\Ecommerce\UserService;

beforeEach(function () {
    // Setup basic features in database manually for testing
    Feature::updateOrCreate(['slug' => 'visibility_multiplier'], [
        'name' => 'Visibility Multiplier',
        'slug' => 'visibility_multiplier',
        'is_implemented' => true
    ]);
    Feature::updateOrCreate(['slug' => 'job_priority'], [
        'name' => 'Job Priority',
        'slug' => 'job_priority',
        'is_implemented' => true
    ]);
    Feature::updateOrCreate(['slug' => 'dm_recruiters_msme'], [
        'name' => 'DM Recruiters',
        'slug' => 'dm_recruiters_msme',
        'is_implemented' => true
    ]);
    Feature::updateOrCreate(['slug' => 'sell_services'], [
        'name' => 'Sell Services',
        'slug' => 'sell_services',
        'is_implemented' => true
    ]);
    Feature::updateOrCreate(['slug' => 'ads_frequency'], [
        'name' => 'Ads Frequency',
        'slug' => 'ads_frequency',
        'is_implemented' => false
    ]);
});

test('user trait falls back to default values when no active subscription exists', function () {
    $user = User::factory()->create();

    // No active subscription
    expect($user->getFeatureLimit('visibility_multiplier'))->toBe('1.0x');
    expect($user->getFeatureLimit('job_priority'))->toBe('0');
    expect($user->getFeatureLimit('dm_recruiters_msme'))->toBe('No');
    expect($user->getFeatureLimit('sell_services'))->toBe('No');
    // Non-implemented feature check
    expect($user->getFeatureLimit('ads_frequency'))->toBe('High');
});

test('user trait retrieves active subscription feature limit value', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'type' => 'personal',
        'status' => 'active',
        'is_active' => true,
        'is_default' => true,
    ]);

    $plan = SubscriptionPlan::create([
        'profile_type' => 'personal',
        'name' => 'Test Premium Plan',
        'slug' => 'test-premium-plan',
        'price' => 299.00,
        'currency' => 'INR',
        'duration_days' => 30,
        'is_active' => true,
        'is_default' => false,
    ]);

    $feature = Feature::where('slug', 'visibility_multiplier')->first();
    $plan->features()->attach($feature->id, ['limit_value' => '2.5x']);

    UserSubscription::create([
        'user_id' => $user->id,
        'profile_id' => $profile->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'status' => 'active',
    ]);

    expect($user->getFeatureLimit('visibility_multiplier'))->toBe('2.5x');
});

test('job application list is sorted by job_priority value descending', function () {
    $employer = User::factory()->create(['is_employer' => true]);
    $employerToken = $employer->createToken('test')->plainTextToken;

    $job = UserJobPost::create([
        'employer_id' => $employer->id,
        'title' => 'Software Engineer',
        'slug' => 'software-engineer-' . rand(1000, 9999),
        'company_name' => 'Tech Corp',
        'status' => 'published',
    ]);

    // Candidate 1: default plan (priority 0)
    $candidate1 = User::factory()->create();
    $profile1 = Profile::create(['user_id' => $candidate1->id, 'type' => 'personal', 'status' => 'active', 'is_active' => true, 'is_default' => true]);
    $app1 = UserJobApplication::create([
        'job_id' => $job->id,
        'applicant_id' => $candidate1->id,
        'status' => 'applied',
        'applied_at' => now()->subMinutes(10),
    ]);

    // Candidate 2: High priority plan (priority 5)
    $candidate2 = User::factory()->create();
    $profile2 = Profile::create(['user_id' => $candidate2->id, 'type' => 'personal', 'status' => 'active', 'is_active' => true, 'is_default' => true]);
    
    $plan = SubscriptionPlan::create([
        'profile_type' => 'personal',
        'name' => 'High Priority Plan',
        'slug' => 'high-priority-plan',
        'price' => 999.00,
        'is_active' => true,
    ]);
    
    $feature = Feature::where('slug', 'job_priority')->first();
    $plan->features()->attach($feature->id, ['limit_value' => '5']);

    UserSubscription::create([
        'user_id' => $candidate2->id,
        'profile_id' => $profile2->id,
        'subscription_plan_id' => $plan->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'status' => 'active',
    ]);

    $app2 = UserJobApplication::create([
        'job_id' => $job->id,
        'applicant_id' => $candidate2->id,
        'status' => 'applied',
        'applied_at' => now(),
    ]);

    $response = $this->withToken($employerToken)
        ->getJson("/api/v1/jobs/{$job->id}/applications");

    $response->assertOk();
    $data = $response->json('data.data');

    // Candidate 2 (app2) should be listed first due to higher priority plan
    expect($data[0]['id'])->toBe($app2->id);
    expect($data[1]['id'])->toBe($app1->id);
});

test('unsubscribed users cannot message recruiter/business profiles', function () {
    $sender = User::factory()->create();
    $senderToken = $sender->createToken('test')->plainTextToken;

    $recruiter = User::factory()->create(['is_employer' => true]);

    $response = $this->withToken($senderToken)
        ->postJson('/api/v1/chats/private', [
            'other_user_id' => $recruiter->id
        ]);

    // Should return 400 or 500 error due to thrown exception in validation
    $response->assertStatus(400);
});

test('unsubscribed users cannot sell services', function () {
    $seller = User::factory()->create();
    $sellerToken = $seller->createToken('test')->plainTextToken;

    $response = $this->withToken($sellerToken)
        ->postJson('/api/v1/seller/services', [
            'title' => 'Web Design Services',
            'description' => 'I will design your website',
            'price' => 1000,
        ]);

    $response->assertStatus(403);
});
