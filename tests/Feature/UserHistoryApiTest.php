<?php

uses(Illuminate\Foundation\Testing\DatabaseTransactions::class);

use App\Models\Comment;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserOrderItem;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use App\Models\Like;
use App\Models\User;
use App\Models\UserPost;
use App\Models\View;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Clean up tables that may have data from other test files
    View::query()->delete();
    Like::query()->delete();
    Comment::query()->delete();
    UserOrderItem::query()->delete();
    UserOrder::query()->delete();
    UserProduct::query()->delete();
    UserService::query()->delete();
    UserPost::query()->delete();
    User::where('email', 'not like', '%.example.com')->delete();

    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');

    Role::firstOrCreate(['name' => 'admin']);
});

// ─── LIKE HISTORY ───────────────────────────────────────────────────

test('user can fetch like history', function () {
    $posts = UserPost::factory(3)->create(['user_id' => $this->user->id]);

    foreach ($posts as $post) {
        Like::create(['user_id' => $this->user->id, 'likeable_type' => UserPost::class, 'likeable_id' => $post->id]);
    }

    $response = $this->getJson('/api/v1/history/likes');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data');
});

test('like history returns correct structure', function () {
    $post = UserPost::factory()->create(['user_id' => $this->user->id]);
    Like::create(['user_id' => $this->user->id, 'likeable_type' => UserPost::class, 'likeable_id' => $post->id]);

    $response = $this->getJson('/api/v1/history/likes');

    $response->assertOk();
    $item = $response->json('data.0');
    expect($item)->toHaveKeys(['id', 'entity_type', 'entity_id', 'preview', 'created_at', 'created_human']);
});

// ─── COMMENT HISTORY ────────────────────────────────────────────────

test('user can fetch comment history', function () {
    $post = UserPost::factory()->create(['user_id' => $this->user->id]);
    Comment::create([
        'user_id' => $this->user->id,
        'body' => 'Great post!',
        'commentable_type' => UserPost::class,
        'commentable_id' => $post->id,
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/v1/history/comments');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.body', 'Great post!');
});

// ─── VIDEO VIEW HISTORY ─────────────────────────────────────────────

test('video views filters reels only', function () {
    View::query()->delete();
    $reel = UserPost::factory()->create(['user_id' => $this->user->id, 'type' => 'reel']);
    $video = UserPost::factory()->create(['user_id' => $this->user->id, 'type' => 'video']);

    View::create(['user_id' => $this->user->id, 'viewable_type' => (new UserPost)->getMorphClass(), 'viewable_id' => $reel->id]);
    View::create(['user_id' => $this->user->id, 'viewable_type' => (new UserPost)->getMorphClass(), 'viewable_id' => $video->id]);

    $response = $this->getJson('/api/v1/history/video-views?filter=reels');

    $response->assertOk()->assertJsonCount(1, 'data');
    expect($response->json('data.0.post_type'))->toBe('reel');
});

test('video views filters long video only', function () {
    View::query()->delete();
    $reel = UserPost::factory()->create(['user_id' => $this->user->id, 'type' => 'reel']);
    $video = UserPost::factory()->create(['user_id' => $this->user->id, 'type' => 'video']);

    View::create(['user_id' => $this->user->id, 'viewable_type' => (new UserPost)->getMorphClass(), 'viewable_id' => $reel->id]);
    View::create(['user_id' => $this->user->id, 'viewable_type' => (new UserPost)->getMorphClass(), 'viewable_id' => $video->id]);

    $response = $this->getJson('/api/v1/history/video-views?filter=long_video');

    $response->assertOk()->assertJsonCount(1, 'data');
    expect($response->json('data.0.post_type'))->toBe('video');
});

test('video views defaults to both', function () {
    View::query()->delete();
    $reel = UserPost::factory()->create(['user_id' => $this->user->id, 'type' => 'reel']);
    $video = UserPost::factory()->create(['user_id' => $this->user->id, 'type' => 'video']);

    View::create(['user_id' => $this->user->id, 'viewable_type' => (new UserPost)->getMorphClass(), 'viewable_id' => $reel->id]);
    View::create(['user_id' => $this->user->id, 'viewable_type' => (new UserPost)->getMorphClass(), 'viewable_id' => $video->id]);

    $response = $this->getJson('/api/v1/history/video-views');

    $response->assertOk()->assertJsonCount(2, 'data');
});

test('invalid video view filter returns 422', function () {
    $response = $this->getJson('/api/v1/history/video-views?filter=invalid');
    $response->assertStatus(422);
});

// ─── PURCHASE HISTORY ───────────────────────────────────────────────

test('user can fetch purchase history', function () {
    $order = UserOrder::create([
        'uuid' => 'test-uuid-1',
        'buyer_id' => $this->user->id,
        'total_amount' => 99.99,
        'status' => 'delivered',
        'payment_status' => 'paid',
    ]);

    $product = UserProduct::create([
        'seller_id' => $this->user->id,
        'title' => 'Test Product',
        'slug' => 'test-product',
        'price' => 99.99,
        'status' => 'approved',
    ]);

    UserOrderItem::create([
        'uuid' => 'item-uuid-1',
        'order_id' => $order->id,
        'seller_id' => $this->user->id,
        'item_type' => 'user_products',
        'item_id' => $product->id,
        'quantity' => 1,
        'price' => 99.99,
    ]);

    $response = $this->getJson('/api/v1/history/purchases');

    $response->assertOk()->assertJsonCount(1, 'data');
    expect($response->json('data.0.items.0.title'))->toBe('Test Product');
});

// ─── SERVICE HISTORY ────────────────────────────────────────────────

test('user can fetch service history', function () {
    $order = UserOrder::create([
        'uuid' => 'test-uuid-2',
        'buyer_id' => $this->user->id,
        'total_amount' => 149.99,
        'status' => 'paid',
        'payment_status' => 'paid',
    ]);

    $service = UserService::create([
        'seller_id' => $this->user->id,
        'title' => 'Web Design',
        'slug' => 'web-design',
        'price' => 149.99,
        'status' => 'approved',
    ]);

    UserOrderItem::create([
        'uuid' => 'item-uuid-2',
        'order_id' => $order->id,
        'seller_id' => $this->user->id,
        'item_type' => 'user_services',
        'item_id' => $service->id,
        'quantity' => 1,
        'price' => 149.99,
    ]);

    $response = $this->getJson('/api/v1/history/services');

    $response->assertOk()->assertJsonCount(1, 'data');
    expect($response->json('data.0.items.0.title'))->toBe('Web Design');
});

// ─── PAGINATION ─────────────────────────────────────────────────────

test('pagination returns cursor metadata', function () {
    $posts = UserPost::factory(25)->create(['user_id' => $this->user->id]);
    foreach ($posts as $post) {
        Like::create(['user_id' => $this->user->id, 'likeable_type' => UserPost::class, 'likeable_id' => $post->id]);
    }

    $response = $this->getJson('/api/v1/history/likes?per_page=10');

    $response->assertOk();
    $meta = $response->json('meta');
    expect($meta)->toHaveKeys(['next_cursor', 'per_page', 'has_more']);
    expect($meta['per_page'])->toBe(10);
    expect($meta['has_more'])->toBeTrue();
});

test('per_page is capped at 50', function () {
    $posts = UserPost::factory(60)->create(['user_id' => $this->user->id]);
    foreach ($posts as $post) {
        Like::create(['user_id' => $this->user->id, 'likeable_type' => UserPost::class, 'likeable_id' => $post->id]);
    }

    $response = $this->getJson('/api/v1/history/likes?per_page=100');

    $response->assertOk();
    expect($response->json('meta.per_page'))->toBe(50);
});

// ─── AUTHENTICATION ─────────────────────────────────────────────────

test('unauthenticated user gets 401', function () {
    auth()->guard('sanctum')->forgetUser();

    foreach (['likes', 'comments', 'video-views', 'purchases', 'services'] as $endpoint) {
        $this->getJson("/api/v1/history/$endpoint")->assertStatus(401);
    }
});

// ─── CONCURRENCY ────────────────────────────────────────────────────

test('concurrent likes produce correct count', function () {
    $post = UserPost::factory()->create(['user_id' => $this->user->id, 'like_count' => 0]);

    $users = User::factory(10)->create();

    $start = microtime(true);

    $users->each(function ($u) use ($post) {
        \Illuminate\Support\Facades\DB::transaction(function () use ($post, $u) {
            $locked = $post->newQuery()->whereKey($post->id)->lockForUpdate()->firstOrFail();
            $locked->likes()->create(['user_id' => $u->id]);
            $locked->increment('like_count');
        });
    });

    $post->refresh();
    expect($post->like_count)->toBe(10);
});

// ─── SOFT DELETE PLACEHOLDER ────────────────────────────────────────

test('soft deleted entity preview shows deleted placeholder', function () {
    $post = UserPost::factory()->create(['user_id' => $this->user->id, 'caption' => 'Original']);
    Like::create(['user_id' => $this->user->id, 'likeable_type' => UserPost::class, 'likeable_id' => $post->id]);

    $post->delete();

    $response = $this->getJson('/api/v1/history/likes');
    $response->assertOk();
    $preview = $response->json('data.0.preview');
    expect($preview['caption'])->toContain('[deleted]');
    expect($preview['thumbnail'])->toBeNull();
});

// ─── STABLE SORT ────────────────────────────────────────────────────

test('stable sort prevents duplicates with same created_at', function () {
    $now = now();
    $posts = UserPost::factory(5)->create(['user_id' => $this->user->id]);

    foreach ($posts as $i => $post) {
        $like = new Like(['user_id' => $this->user->id, 'likeable_type' => UserPost::class, 'likeable_id' => $post->id]);
        $like->created_at = $now;
        $like->save();
        $like->timestamps = false;
    }

    $response = $this->getJson('/api/v1/history/likes?per_page=3');
    $response->assertOk();
    expect(count($response->json('data')))->toBe(3);

    $ids = collect($response->json('data'))->pluck('id');
    expect($ids->duplicates()->isEmpty())->toBeTrue();
});
