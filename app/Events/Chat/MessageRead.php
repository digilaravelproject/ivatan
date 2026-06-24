<?php

namespace App\Events\Chat;

use App\Models\Chat\UserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $userId;
    public int $lastReadMessageId;
    public ?int $targetUserId;

    public function __construct(int $chatId, int $userId, int $lastReadMessageId, ?int $targetUserId = null)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->lastReadMessageId = $lastReadMessageId;
        $this->targetUserId = $targetUserId;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];

        if ($this->targetUserId) {
            $channels[] = new PrivateChannel('private-user.' . $this->targetUserId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'last_read_message_id' => $this->lastReadMessageId,
            'read_by' => $this->userId,
            'read_at' => now()->toISOString(),
        ];
    }
}
