<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantRemoved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $removedUserId;
    public int $removedBy;

    public function __construct(int $chatId, int $removedUserId, int $removedBy)
    {
        $this->chatId = $chatId;
        $this->removedUserId = $removedUserId;
        $this->removedBy = $removedBy;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
            new PrivateChannel('private-user.' . $this->removedUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.removed';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'removed_user_id' => $this->removedUserId,
            'removed_by' => $this->removedBy,
        ];
    }
}
