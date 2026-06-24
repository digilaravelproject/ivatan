<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCallSession;
use App\Models\Chat\UserChat;
use App\Events\Chat\CallInitiated;
use App\Events\Chat\CallRinging;
use App\Events\Chat\CallAccepted;
use App\Events\Chat\CallDeclined;
use App\Events\Chat\CallCancelled;
use App\Events\Chat\CallBusy;
use App\Events\Chat\IceCandidateExchange;
use App\Events\Chat\CallEnded;
use App\Jobs\CleanupMissedCallJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CallService
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Initiate a call session
     */
    public function initiateCall(User $user, int $chatId, string $type, array $sdpOffer): UserCallSession
    {
        $chat = UserChat::findOrFail($chatId);

        $isParticipant = $chat->participants()->where('user_id', $user->id)->exists();
        if (!$isParticipant) {
            throw new \Exception('Unauthorized to call in this chat.', 403);
        }

        $session = DB::transaction(function () use ($chat, $user, $type) {
            // Lock caller record
            $caller = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
            if ($caller->is_busy) {
                throw new \RuntimeException('You are currently busy.', 409);
            }

            $receiverId = null;
            if ($chat->type === 'private') {
                $otherParticipant = $chat->participants()->where('user_id', '!=', $user->id)->first();
                if (!$otherParticipant) {
                    throw new \RuntimeException('No receiver found in this chat.', 404);
                }
                $receiverId = $otherParticipant->user_id;

                // Lock receiver record
                $receiver = User::where('id', $receiverId)->lockForUpdate()->firstOrFail();

                $this->chatService->validateMessagingPrivacy($caller, $receiver);

                if ($receiver->is_busy) {
                    throw new \RuntimeException('User is currently busy.', 409);
                }
            }

            $session = UserCallSession::create([
                'uuid' => (string) Str::uuid(),
                'chat_id' => $chat->id,
                'caller_id' => $caller->id,
                'receiver_id' => $receiverId,
                'type' => $type,
                'status' => 'ringing',
            ]);

            $caller->update(['is_busy' => true, 'busy_status' => 'calling_' . $type]);

            return $session;
        });

        if ($session->receiver_id) {
            $callerInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->profile_photo_url,
            ];
            broadcast(new CallInitiated(
                $session->receiver_id,
                $session->uuid,
                $callerInfo,
                $session->type,
                $sdpOffer
            ))->toOthers();
        }

        CleanupMissedCallJob::dispatch($session->uuid)->delay(now()->addSeconds(60));

        return $session;
    }

    /**
     * Send Ringing state back to caller
     */
    public function ringingCall(User $user, string $uuid): void
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();

        if ($user->id !== $session->receiver_id) {
            throw new \Exception('Unauthorized to send ringing.', 403);
        }

        broadcast(new CallRinging($session->caller_id, $session->uuid))->toOthers();
    }

    /**
     * Accept call (receiver side)
     */
    public function acceptCall(User $user, string $uuid, array $sdpAnswer): UserCallSession
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if ($user->id !== $session->receiver_id) {
            throw new \Exception('Unauthorized to accept this call.', 403);
        }

        if ($session->status !== 'ringing') {
            throw new \Exception('Call session is no longer active.', 400);
        }

        $session->update([
            'status' => 'accepted',
            'started_at' => now(),
        ]);

        $user->update(['is_busy' => true, 'busy_status' => 'calling_' . $session->type]);

        broadcast(new CallAccepted($session->caller_id, $session->uuid, $sdpAnswer))->toOthers();

        return $session;
    }

    /**
     * Decline call (receiver side)
     */
    public function declineCall(User $user, string $uuid, ?string $reason = 'declined'): UserCallSession
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if ($user->id !== $session->receiver_id) {
            throw new \Exception('Unauthorized to decline this call.', 403);
        }

        if ($session->status !== 'ringing') {
            throw new \Exception('Call session is no longer active.', 400);
        }

        $status = $reason === 'busy' ? 'busy' : 'rejected';

        $session->update([
            'status' => $status,
            'ended_at' => now(),
        ]);

        broadcast(new CallDeclined($session->caller_id, $session->uuid, $reason))->toOthers();

        User::where('id', $session->caller_id)->update(['is_busy' => false, 'busy_status' => null]);

        return $session;
    }

    /**
     * Cancel call (caller side)
     */
    public function cancelCall(User $user, string $uuid): UserCallSession
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if ($user->id !== $session->caller_id) {
            throw new \Exception('Unauthorized to cancel this call.', 403);
        }

        $session->update([
            'status' => 'cancelled',
            'ended_at' => now(),
        ]);

        if ($session->receiver_id) {
            broadcast(new CallCancelled($session->receiver_id, $session->uuid))->toOthers();
        }

        $user->update(['is_busy' => false, 'busy_status' => null]);

        return $session;
    }

    /**
     * Busy call state
     */
    public function busyCall(User $user, string $uuid): UserCallSession
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if ($user->id !== $session->receiver_id) {
            throw new \Exception('Unauthorized.', 403);
        }

        $session->update([
            'status' => 'busy',
            'ended_at' => now(),
        ]);

        broadcast(new CallBusy($session->caller_id, $session->uuid))->toOthers();

        User::where('id', $session->caller_id)->update(['is_busy' => false, 'busy_status' => null]);

        return $session;
    }

    /**
     * End call session (either side)
     */
    public function endCall(User $user, string $uuid): UserCallSession
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if ($user->id !== $session->caller_id && $user->id !== $session->receiver_id) {
            throw new \Exception('Unauthorized to end this call.', 403);
        }

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $peerId = $user->id === $session->caller_id ? $session->receiver_id : $session->caller_id;

        if ($peerId) {
            broadcast(new CallEnded($peerId, $session->uuid))->toOthers();
        }

        $user->update(['is_busy' => false, 'busy_status' => null]);

        if ($peerId) {
            User::withoutEvents(function () use ($peerId) {
                User::where('id', $peerId)->update(['is_busy' => false, 'busy_status' => null]);
            });
        }

        return $session;
    }

    /**
     * Route ICE Candidate
     */
    public function exchangeIceCandidate(User $user, string $uuid, array $candidate): void
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if ($user->id !== $session->caller_id && $user->id !== $session->receiver_id) {
            throw new \Exception('Unauthorized to exchange candidate.', 403);
        }

        $peerId = $user->id === $session->caller_id ? $session->receiver_id : $session->caller_id;

        if ($peerId) {
            broadcast(new IceCandidateExchange($peerId, $session->uuid, $candidate))->toOthers();
        }
    }
}
