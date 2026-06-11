<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public array $updatedFields;

    public function __construct(int $chatId, array $updatedFields)
    {
        $this->chatId = $chatId;
        $this->updatedFields = $updatedFields;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'group.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'updated_fields' => $this->updatedFields,
        ];
    }
}
