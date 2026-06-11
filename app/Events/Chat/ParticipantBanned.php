<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantBanned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $userId;

    public function __construct(int $chatId, int $userId)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.banned';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'is_banned' => true,
        ];
    }
}
