<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $userId;
    public bool $isAdmin;
    public int $updatedBy;

    public function __construct(int $chatId, int $userId, bool $isAdmin, int $updatedBy)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
        $this->updatedBy = $updatedBy;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'is_admin' => $this->isAdmin,
            'updated_by' => $this->updatedBy,
        ];
    }
}
