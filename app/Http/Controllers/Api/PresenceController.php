<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PresenceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
    use ApiResponse;

    protected PresenceService $presenceService;

    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    public function pulse(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'is_busy' => 'nullable|boolean',
            'busy_status' => 'nullable|string|in:chatting,calling_audio,calling_video,null',
        ]);

        if ($request->has('is_busy')) {
            if ($request->boolean('is_busy') && $request->has('busy_status')) {
                $this->presenceService->setBusy($user, $request->busy_status);
            } elseif (!$request->boolean('is_busy')) {
                $this->presenceService->setFree($user);
            }
        }

        $user->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        $this->presenceService->setOnline($user);

        return $this->success([
            'is_online' => true,
            'last_seen_at' => now()->toISOString(),
        ], 'Presence updated.');
    }

    public function offline(): JsonResponse
    {
        $user = Auth::user();
        $this->presenceService->setOffline($user);

        return $this->success(null, 'User set offline.');
    }
}
