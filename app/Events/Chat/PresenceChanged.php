<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PresenceChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $userId;
    public bool $isOnline;
    public ?string $lastSeenAt;

    public function __construct(int $chatId, int $userId, bool $isOnline, ?string $lastSeenAt)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->isOnline = $isOnline;
        $this->lastSeenAt = $lastSeenAt;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'presence.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'last_seen_at' => $this->lastSeenAt,
        ];
    }
}
