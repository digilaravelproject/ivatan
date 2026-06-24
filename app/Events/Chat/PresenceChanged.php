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
        $channels = [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];

        // For private chats, also broadcast to the other user's private channel so that the inbox/chat listing updates the online status in real-time
        $chat = \App\Models\Chat\UserChat::find($this->chatId);
        if ($chat && $chat->type === 'private') {
            $otherParticipant = $chat->participants()->where('user_id', '!=', $this->userId)->first();
            if ($otherParticipant) {
                $channels[] = new \Illuminate\Broadcasting\PrivateChannel('private-user.' . $otherParticipant->user_id);
            }
        }

        return $channels;
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
