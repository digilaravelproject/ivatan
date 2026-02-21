<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $userId;
    public int $lastReadMessageId;

    public function __construct(int $chatId, int $userId, int $lastReadMessageId)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->lastReadMessageId = $lastReadMessageId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'last_read_message_id' => $this->lastReadMessageId,
            'read_at' => now()->toISOString(),
        ];
    }
}
