<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCallSession;
use App\Models\Chat\UserChat;
use App\Services\ChatService;
use App\Events\Chat\CallInitiated;
use App\Events\Chat\CallRinging;
use App\Events\Chat\CallAccepted;
use App\Events\Chat\CallDeclined;
use App\Events\Chat\CallCancelled;
use App\Events\Chat\CallBusy;
use App\Events\Chat\IceCandidateExchange;
use App\Events\Chat\CallEnded;
use App\Jobs\CleanupMissedCallJob;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CallController extends Controller
{
    use ApiResponse;

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Initiate a Call Session (caller side)
     */
    public function initiate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'chat_id' => 'required|exists:user_chats,id',
                'type' => 'required|in:audio,video',
                'sdp_offer' => 'required|array',
            ]);

            $chat = UserChat::findOrFail($request->chat_id);

            $isParticipant = $chat->participants()->where('user_id', Auth::id())->exists();
            if (!$isParticipant) {
                return $this->error('Unauthorized to call in this chat.', 403);
            }

            try {
                $session = \Illuminate\Support\Facades\DB::transaction(function () use ($chat, $request) {
                    // Lock caller record
                    $user = User::where('id', Auth::id())->lockForUpdate()->firstOrFail();
                    if ($user->is_busy) {
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

                        $this->chatService->validateMessagingPrivacy($user, $receiver);

                        if ($receiver->is_busy) {
                            throw new \RuntimeException('User is currently busy.', 409);
                        }
                    }

                    $session = UserCallSession::create([
                        'uuid' => (string) Str::uuid(),
                        'chat_id' => $chat->id,
                        'caller_id' => $user->id,
                        'receiver_id' => $receiverId,
                        'type' => $request->type,
                        'status' => 'ringing',
                    ]);

                    $user->update(['is_busy' => true, 'busy_status' => 'calling_' . $request->type]);

                    return $session;
                });
            } catch (\RuntimeException $e) {
                $code = $e->getCode();
                return $this->error($e->getMessage(), ($code >= 400 && $code < 600) ? $code : 400);
            }

            $user = Auth::user();
            $receiverId = $session->receiver_id;

            if ($receiverId) {
                $callerInfo = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'avatar' => $user->profile_photo_url,
                ];
                broadcast(new CallInitiated(
                    $receiverId,
                    $session->uuid,
                    $callerInfo,
                    $session->type,
                    $request->sdp_offer
                ))->toOthers();
            }

            CleanupMissedCallJob::dispatch($session->uuid)->delay(now()->addSeconds(60));

            return $this->success([
                'session' => $session
            ], 'Call initiated.', 201);
        } catch (\Throwable $e) {
            \Log::error('CallController initiate error', [
                'error' => $e->getMessage(),
                'chat_id' => $request->chat_id ?? null,
            ]);
            return $this->error('Failed to initiate call.', 500);
        }
    }

    /**
     * Send Ringing state back to caller
     */
    public function ringing(string $uuid): JsonResponse
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();

        if (Auth::id() !== $session->receiver_id) {
            return $this->error('Unauthorized to send ringing.', 403);
        }

        broadcast(new CallRinging($session->caller_id, $session->uuid))->toOthers();

        return $this->success(null, 'Ringing broadcast sent.');
    }

    /**
     * Accept call (receiver side)
     */
    public function accept(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'sdp_answer' => 'required|array',
        ]);

        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if (Auth::id() !== $session->receiver_id) {
            return $this->error('Unauthorized to accept this call.', 403);
        }

        if ($session->status !== 'ringing') {
            return $this->error('Call session is no longer active.', 400);
        }

        $session->update([
            'status' => 'accepted',
            'started_at' => now(),
        ]);

        Auth::user()->update(['is_busy' => true, 'busy_status' => 'calling_' . $session->type]);

        broadcast(new CallAccepted($session->caller_id, $session->uuid, $request->sdp_answer))->toOthers();

        return $this->success([
            'session' => $session
        ], 'Call accepted.');
    }

    /**
     * Decline call (receiver side)
     */
    public function decline(Request $request, string $uuid): JsonResponse
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if (Auth::id() !== $session->receiver_id) {
            return $this->error('Unauthorized to decline this call.', 403);
        }

        if ($session->status !== 'ringing') {
            return $this->error('Call session is no longer active.', 400);
        }

        $reason = $request->input('reason', 'declined'); // declined, busy
        $status = $reason === 'busy' ? 'busy' : 'rejected';

        $session->update([
            'status' => $status,
            'ended_at' => now(),
        ]);

        broadcast(new CallDeclined($session->caller_id, $session->uuid, $reason))->toOthers();

        User::where('id', $session->caller_id)->update(['is_busy' => false, 'busy_status' => null]);

        return $this->success([
            'session' => $session
        ], 'Call declined.');
    }

    /**
     * Cancel call (caller side)
     */
    public function cancel(string $uuid): JsonResponse
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if (Auth::id() !== $session->caller_id) {
            return $this->error('Unauthorized to cancel this call.', 403);
        }

        $session->update([
            'status' => 'cancelled',
            'ended_at' => now(),
        ]);

        if ($session->receiver_id) {
            broadcast(new CallCancelled($session->receiver_id, $session->uuid))->toOthers();
        }

        Auth::user()->update(['is_busy' => false, 'busy_status' => null]);

        return $this->success([
            'session' => $session
        ], 'Call cancelled.');
    }

    /**
     * Busy call state
     */
    public function busy(string $uuid): JsonResponse
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        if (Auth::id() !== $session->receiver_id) {
            return $this->error('Unauthorized.', 403);
        }

        $session->update([
            'status' => 'busy',
            'ended_at' => now(),
        ]);

        broadcast(new CallBusy($session->caller_id, $session->uuid))->toOthers();

        User::where('id', $session->caller_id)->update(['is_busy' => false, 'busy_status' => null]);

        return $this->success([
            'session' => $session
        ], 'Call busy state set.');
    }

    /**
     * End call session (either side)
     */
    public function end(string $uuid): JsonResponse
    {
        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        $userId = Auth::id();
        if ($userId !== $session->caller_id && $userId !== $session->receiver_id) {
            return $this->error('Unauthorized to end this call.', 403);
        }

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $peerId = $userId === $session->caller_id ? $session->receiver_id : $session->caller_id;

        if ($peerId) {
            broadcast(new CallEnded($peerId, $session->uuid))->toOthers();
        }

        Auth::user()->update(['is_busy' => false, 'busy_status' => null]);

        if ($peerId) {
            User::withoutEvents(function () use ($peerId) {
                User::where('id', $peerId)->update(['is_busy' => false, 'busy_status' => null]);
            });
        }

        return $this->success([
            'session' => $session
        ], 'Call ended.');
    }

    /**
     * Route WebRTC ICE Candidate
     */
    public function iceCandidate(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'candidate' => 'required|array',
        ]);

        $session = UserCallSession::where('uuid', $uuid)->firstOrFail();
        
        $userId = Auth::id();
        if ($userId !== $session->caller_id && $userId !== $session->receiver_id) {
            return $this->error('Unauthorized to exchange candidate.', 403);
        }

        $peerId = $userId === $session->caller_id ? $session->receiver_id : $session->caller_id;

        if ($peerId) {
            broadcast(new IceCandidateExchange($peerId, $session->uuid, $request->candidate))->toOthers();
        }

        return $this->success(null, 'ICE Candidate broadcasted.');
    }
}
