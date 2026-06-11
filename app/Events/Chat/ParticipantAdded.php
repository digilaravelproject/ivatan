<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public array $addedUser;
    public int $addedBy;

    public function __construct(int $chatId, array $addedUser, int $addedBy)
    {
        $this->chatId = $chatId;
        $this->addedUser = $addedUser;
        $this->addedBy = $addedBy;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
            new PrivateChannel('private-user.' . $this->addedUser['id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.added';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'added_user' => $this->addedUser,
            'added_by' => $this->addedBy,
        ];
    }
}
