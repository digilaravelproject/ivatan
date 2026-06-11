<?php

namespace App\Services;

use App\Events\Chat\PresenceChanged;
use App\Models\User;
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

}
