<?php

use App\Models\User;
use App\Models\UserPost;
use App\Models\ExclusiveContentPurchase;
use App\Models\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('allows authenticated creator to retrieve global dashboard stats', function () {
    $creator = User::factory()->create();
    $buyer = User::factory()->create();

    $post = UserPost::factory()->create([
        'user_id' => $creator->id,
        'is_exclusive' => true,
        'status' => 'active',
        'type' => 'post',
        'price' => 100.00,
        'view_count' => 10,
    ]);

    ExclusiveContentPurchase::create([
        'buyer_id' => $buyer->id,
        'user_post_id' => $post->id,
        'creator_price' => 100.00,
        'platform_fee_charged' => 10.00,
        'gateway_charge_amount' => 2.00,
        'gateway_charge_bearer' => 'buyer',
        'final_paid_amount' => 112.00,
        'status' => 'completed',
    ]);

    View::create([
        'user_id' => $buyer->id,
        'viewable_id' => $post->id,
        'viewable_type' => UserPost::class,
    ]);

    Sanctum::actingAs($creator);

    $response = $this->getJson('/api/v1/creator/dashboard/stats');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'global_stats' => [
                    'global_total_earnings' => 100.00,
                    'global_total_purchases' => 1,
                    'global_total_exclusive_content' => 1,
                ],
            ],
        ]);
});

it('allows authenticated creator to retrieve item-level exclusive content stats', function () {
    $creator = User::factory()->create();
    $buyer = User::factory()->create();

    $post = UserPost::factory()->create([
        'user_id' => $creator->id,
        'is_exclusive' => true,
        'status' => 'active',
        'type' => 'reel',
        'price' => 50.00,
    ]);

    ExclusiveContentPurchase::create([
        'buyer_id' => $buyer->id,
        'user_post_id' => $post->id,
        'creator_price' => 50.00,
        'platform_fee_charged' => 5.00,
        'gateway_charge_amount' => 1.00,
        'gateway_charge_bearer' => 'buyer',
        'final_paid_amount' => 56.00,
        'status' => 'completed',
    ]);

    View::create([
        'user_id' => $buyer->id,
        'viewable_id' => $post->id,
        'viewable_type' => UserPost::class,
    ]);

    Sanctum::actingAs($creator);

    $response = $this->getJson('/api/v1/creator/dashboard/exclusive-content?sort_by=earnings&order=desc');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $data = $response->json('data.data');
    expect($data)->toHaveCount(1);
    expect($data[0]['id'])->toBe($post->id);
    expect($data[0]['total_earnings'])->toBe(50.0);
    expect($data[0]['total_purchase_count'])->toBe(1);
    expect($data[0]['purchased_user_views'])->toBe(1);
});

it('prevents unauthenticated user from accessing creator dashboard stats', function () {
    $response = $this->getJson('/api/v1/creator/dashboard/stats');
    $response->assertStatus(401);
});
