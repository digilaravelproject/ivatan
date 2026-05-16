<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PostApiTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user for posts
        $this->user = User::factory()->create(['username' => 'testuser']);
    }

    /** @test */
    public function guest_cannot_access_protected_post_but_api_does_not_crash_if_model_retrieved_elsewhere()
    {
        $post = UserPost::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-1',
            'type' => 'post',
            'status' => 'active',
            'visibility' => 'public',
        ]);

        // Attempting to access protected show route as guest should be 401 Unauthorized
        // but it should NOT be a 500 Internal Server Error (crash in observer)
        $response = $this->getJson("/api/v1/posts/{$post->id}");
        $response->assertStatus(401); 
    }

    /** @test */
    public function authenticated_user_can_view_post_and_increment_views()
    {
        $post = UserPost::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-auth-view',
            'type' => 'post',
            'status' => 'active',
            'visibility' => 'public',
        ]);

        $viewer = User::factory()->create();
        $this->actingAs($viewer, 'sanctum');

        $response = $this->getJson("/api/v1/posts/{$post->id}");
        $response->assertStatus(200);
        
        $post->refresh();
        // Since it's incremented in show() and likely observer fires too, 
        // but ViewTrackingService prevents duplicates.
        $this->assertEquals(1, $post->view_count);
    }

    /** @test */
    public function views_have_24_hour_cooldown()
    {
        $post = UserPost::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-2',
            'type' => 'video',
            'status' => 'active',
            'visibility' => 'public',
        ]);

        $viewer = User::factory()->create();
        $this->actingAs($viewer, 'sanctum');

        // First view
        $this->getJson("/api/v1/posts/{$post->id}");
        $post->refresh();
        $this->assertEquals(1, $post->view_count);

        // Immediate second view from same user should not increment
        $this->getJson("/api/v1/posts/{$post->id}");
        $post->refresh();
        $this->assertEquals(1, $post->view_count);

        // Travel 25 hours forward
        $this->travel(25)->hours();

        // Third view after 24 hours should increment
        $this->getJson("/api/v1/posts/{$post->id}");
        $post->refresh();
        $this->assertEquals(2, $post->view_count);
    }

    /** @test */
    public function authenticated_user_can_toggle_like()
    {
        $post = UserPost::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-3',
            'type' => 'post',
            'status' => 'active',
            'visibility' => 'public',
        ]);

        $viewer = User::factory()->create();
        $this->actingAs($viewer, 'sanctum');

        // Like the post - Route is POST /api/v1/posts/{id}/like
        $response = $this->postJson("/api/v1/posts/{$post->id}/like");
        $response->assertStatus(200);
        $response->assertJsonPath('data.is_liked', true);
        $response->assertJsonPath('data.stats.like_count', 1);

        $post->refresh();
        $this->assertEquals(1, $post->like_count);

        // Unlike the post
        $response = $this->postJson("/api/v1/posts/{$post->id}/like");
        $response->assertStatus(200);
        $response->assertJsonPath('data.is_liked', false);
        $response->assertJsonPath('data.stats.like_count', 0);

        $post->refresh();
        $this->assertEquals(0, $post->like_count);
    }

    /** @test */
    public function feed_includes_is_liked_correctly_and_efficiently()
    {
        $post = UserPost::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-4',
            'type' => 'post',
            'status' => 'active',
            'visibility' => 'public',
        ]);

        $viewer = User::factory()->create();
        $this->actingAs($viewer, 'sanctum');

        // Like it first
        $this->postJson("/api/v1/posts/{$post->id}/like");

        // Fetch user posts feed - Route is GET /api/v1/posts/user/{username}
        $response = $this->getJson("/api/v1/posts/user/testuser");
        $response->assertStatus(200);
        
        // Check if is_liked is true in the resource
        $this->assertTrue($response->json('data.0.stats.is_liked'));
    }
}
