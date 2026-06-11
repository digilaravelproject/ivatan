<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserBlock;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatParticipant;
use App\Models\Chat\UserChatMessage;
use App\Models\UserCallSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use App\Events\Chat\CallInitiated;
use App\Events\Chat\CallAccepted;
use App\Events\Chat\CallDeclined;
use App\Events\Chat\CallCancelled;
use App\Events\Chat\CallBusy;
use App\Events\Chat\IceCandidateExchange;
use App\Events\Chat\CallEnded;
use App\Events\Chat\MessageSent;
use App\Events\Chat\MessageRead;
use App\Events\Chat\MessageDelivered;
use Tests\TestCase;

class ChatPrivacyTest extends TestCase
{
    use DatabaseTransactions;

    protected User $userA;
    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create([
            'name' => 'User A',
            'username' => 'usera',
            'messaging_privacy' => 'everyone',
            'account_privacy' => 'public',
        ]);

        $this->userB = User::factory()->create([
            'name' => 'User B',
            'username' => 'userb',
            'messaging_privacy' => 'everyone',
            'account_privacy' => 'public',
        ]);
    }

    /** @test */
    public function block_relationship_prevents_chat_initiation()
    {
        // User A blocks User B
        UserBlock::create([
            'user_id' => $this->userA->id,
            'blocked_user_id' => $this->userB->id,
        ]);

        $this->actingAs($this->userA, 'sanctum');

        $response = $this->postJson('/api/v1/chats/private', [
            'other_user_id' => $this->userB->id,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function blocked_user_cannot_message_blocker()
    {
        // User A blocks User B
        UserBlock::create([
            'user_id' => $this->userA->id,
            'blocked_user_id' => $this->userB->id,
        ]);

        // Existing chat before block
        $chat = UserChat::create(['type' => 'private']);
        UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $this->userA->id]);
        UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $this->userB->id]);

        $this->actingAs($this->userB, 'sanctum');

        $response = $this->postJson("/api/v1/chats/{$chat->id}/messages", [
            'content' => 'Hello',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_message_someone_with_messaging_privacy_set_to_none()
    {
        $this->userB->update(['messaging_privacy' => 'none']);

        $this->actingAs($this->userA, 'sanctum');

        $response = $this->postJson('/api/v1/chats/private', [
            'other_user_id' => $this->userB->id,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function followers_only_privacy_prevents_non_followers_from_messaging()
    {
        $this->userB->update(['messaging_privacy' => 'followers']);

        // User A is NOT following User B
        $this->actingAs($this->userA, 'sanctum');

        $response = $this->postJson('/api/v1/chats/private', [
            'other_user_id' => $this->userB->id,
        ]);

        $response->assertStatus(403);

        // User A follows User B
        $this->userA->following()->attach($this->userB->id);

        $response = $this->postJson('/api/v1/chats/private', [
            'other_user_id' => $this->userB->id,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function private_account_requires_follow_before_messaging()
    {
        $this->userB->update(['account_privacy' => 'private']);

        // User A is NOT following User B
        $this->actingAs($this->userA, 'sanctum');

        $response = $this->postJson('/api/v1/chats/private', [
            'other_user_id' => $this->userB->id,
        ]);

        $response->assertStatus(403);

        // User A follows User B
        $this->userA->following()->attach($this->userB->id);

        $response = $this->postJson('/api/v1/chats/private', [
            'other_user_id' => $this->userB->id,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function active_participant_can_initiate_ringing_accept_and_end_webrtc_call()
    {
        Event::fake();

        $chat = UserChat::create(['type' => 'private']);
        UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $this->userA->id]);
        UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $this->userB->id]);

        $this->actingAs($this->userA, 'sanctum');

        // 1. Initiate Call
        $response = $this->postJson('/api/v1/chats/calls/initiate', [
            'chat_id' => $chat->id,
            'type' => 'audio',
            'sdp_offer' => ['sdp' => 'offer-data'],
        ]);

        $response->assertStatus(201);
        $uuid = $response->json('data.session.uuid');
        $this->assertNotEmpty($uuid);

        Event::assertDispatched(CallInitiated::class);

        // 2. Ringing Call
        $response = $this->postJson("/api/v1/chats/calls/{$uuid}/ringing");
        $response->assertStatus(200);
        Event::assertDispatched(CallRinging::class);

        // 3. Accept Call (Receiver B)
        $this->actingAs($this->userB, 'sanctum');
        $response = $this->postJson("/api/v1/chats/calls/{$uuid}/accept", [
            'sdp_answer' => ['sdp' => 'answer-data'],
        ]);
        $response->assertStatus(200);
        $this->assertEquals('accepted', $response->json('data.session.status'));
        Event::assertDispatched(CallAccepted::class);

        // 4. ICE Candidate Exchange
        $response = $this->postJson("/api/v1/chats/calls/{$uuid}/ice-candidate", [
            'candidate' => ['candidate' => 'ice-candidate-data'],
        ]);
        $response->assertStatus(200);
        Event::assertDispatched(IceCandidateExchange::class);

        // 5. End Call
        $response = $this->postJson("/api/v1/chats/calls/{$uuid}/end");
        $response->assertStatus(200);
        $this->assertEquals('ended', $response->json('data.session.status'));
        Event::assertDispatched(CallEnded::class);
    }

    /** @test */
    public function non_participant_cannot_initiate_call()
    {
        $chat = UserChat::create(['type' => 'private']);
        UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $this->userB->id]);

        $this->actingAs($this->userA, 'sanctum');

        $response = $this->postJson('/api/v1/chats/calls/initiate', [
            'chat_id' => $chat->id,
            'type' => 'audio',
            'sdp_offer' => ['sdp' => 'offer-data'],
        ]);

        $response->assertStatus(403);
    }
}
