<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDelivered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $messageId;
    public int $receiverId; // delivered_by
    public int $senderId; // broadcast target

    public function __construct(int $chatId, int $messageId, int $receiverId, int $senderId)
    {
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->receiverId = $receiverId;
        $this->senderId = $senderId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->senderId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.delivered';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'delivered_by' => $this->receiverId,
        ];
    }
}
