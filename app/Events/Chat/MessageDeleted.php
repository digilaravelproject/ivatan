<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $messageId;
    public string $deleteType; // everyone / me
    public int $userId;

    public function __construct(int $chatId, int $messageId, string $deleteType, int $userId)
    {
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->deleteType = $deleteType;
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
        return 'message.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'delete_type' => $this->deleteType,
            'deleted_by' => $this->userId,
        ];
    }
}
