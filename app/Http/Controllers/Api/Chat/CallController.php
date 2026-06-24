<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\UserCallSession;
use App\Services\CallService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    use ApiResponse;

    protected CallService $callService;

    public function __construct(CallService $callService)
    {
        $this->callService = $callService;
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

            $session = $this->callService->initiateCall(
                Auth::user(),
                $request->chat_id,
                $request->type,
                $request->sdp_offer
            );

            return $this->success([
                'session' => $session
            ], 'Call initiated.', 201);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Send Ringing state back to caller
     */
    public function ringing(string $uuid): JsonResponse
    {
        try {
            $this->callService->ringingCall(Auth::user(), $uuid);
            return $this->success(null, 'Ringing broadcast sent.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * Accept call (receiver side)
     */
    public function accept(Request $request, string $uuid): JsonResponse
    {
        try {
            $request->validate([
                'sdp_answer' => 'required|array',
            ]);

            $session = $this->callService->acceptCall(Auth::user(), $uuid, $request->sdp_answer);

            return $this->success([
                'session' => $session
            ], 'Call accepted.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * Decline call (receiver side)
     */
    public function decline(Request $request, string $uuid): JsonResponse
    {
        try {
            $reason = $request->input('reason', 'declined');
            $session = $this->callService->declineCall(Auth::user(), $uuid, $reason);

            return $this->success([
                'session' => $session
            ], 'Call declined.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * Cancel call (caller side)
     */
    public function cancel(string $uuid): JsonResponse
    {
        try {
            $session = $this->callService->cancelCall(Auth::user(), $uuid);

            return $this->success([
                'session' => $session
            ], 'Call cancelled.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * Busy call state
     */
    public function busy(string $uuid): JsonResponse
    {
        try {
            $session = $this->callService->busyCall(Auth::user(), $uuid);

            return $this->success([
                'session' => $session
            ], 'Call busy state set.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * End call session (either side)
     */
    public function end(string $uuid): JsonResponse
    {
        try {
            $session = $this->callService->endCall(Auth::user(), $uuid);

            return $this->success([
                'session' => $session
            ], 'Call ended.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * Route WebRTC ICE Candidate
     */
    public function iceCandidate(Request $request, string $uuid): JsonResponse
    {
        try {
            $request->validate([
                'candidate' => 'required|array',
            ]);

            $this->callService->exchangeIceCandidate(Auth::user(), $uuid, $request->candidate);

            return $this->success(null, 'ICE Candidate broadcasted.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
}
