<?php

namespace App\Services;

use App\Events\Chat\PresenceChanged;
use App\Models\User;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatParticipant;
use Illuminate\Support\Facades\Log;

class PresenceService
{
    public function setOnline(User $user): void
    {
        $user->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        $this->broadcastPresenceToChats($user, true);
    }

    public function setOffline(User $user): void
    {
        $user->update([
            'is_online' => false,
            'is_busy' => false,
            'busy_status' => null,
        ]);

        $this->broadcastPresenceToChats($user, false);
    }

    public function setBusy(User $user, string $status = 'calling'): void
    {
        $user->update([
            'is_busy' => true,
            'busy_status' => $status,
        ]);

        $this->broadcastPresenceToChats($user, true);
    }

    public function setFree(User $user): void
    {
        $user->update([
            'is_busy' => false,
            'busy_status' => null,
        ]);

        $this->broadcastPresenceToChats($user, true);
    }

    public function handleMemberLeft(array $member): void
    {
        $userId = $member['id'] ?? null;
        if (!$userId) return;

        $user = User::find($userId);
        if (!$user) return;

        $this->setOffline($user);

        $this->cancelInitiatedSessions($user);
    }

    protected function broadcastPresenceToChats(User $user, bool $isOnline): void
    {
        try {
            $chatIds = UserChatParticipant::where('user_id', $user->id)
                ->pluck('chat_id');

            foreach ($chatIds as $chatId) {
                broadcast(new PresenceChanged(
                    $chatId,
                    $user->id,
                    $isOnline,
                    $user->last_seen_at?->toISOString()
                ))->toOthers();
            }
        } catch (\Exception $e) {
            Log::error('Presence broadcast error: ' . $e->getMessage());
        }
    }

    protected function cancelInitiatedSessions(User $user): void
    {
        $sessions = \App\Models\UserCallSession::where(function ($q) use ($user) {
            $q->where('caller_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })->whereIn('status', ['ringing'])->get();

        foreach ($sessions as $session) {
            $session->update([
                'status' => 'missed',
                'ended_at' => now(),
            ]);
        }
    }
}
