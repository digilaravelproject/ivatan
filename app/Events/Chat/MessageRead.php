<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    // use Dispatchable, InteractsWithSockets, SerializesModels;
    use Dispatchable, SerializesModels;
    public int $chatId;
    public int $userId;
    public int $lastReadMessageId;
    /**
     * Create a new event instance.
     */
    public function __construct(int $chatId, int $userId, int $lastReadMessageId)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->lastReadMessageId = $lastReadMessageId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chatId),
        ];
    }
    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'last_read_message_id' => $this->lastReadMessageId,
        ];
    }
}
