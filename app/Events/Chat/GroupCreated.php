<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public string $groupName;
    public ?string $avatarUrl;
    public int $createdBy;
    public int $participantId;

    public function __construct(int $chatId, string $groupName, ?string $avatarUrl, int $createdBy, int $participantId)
    {
        $this->chatId = $chatId;
        $this->groupName = $groupName;
        $this->avatarUrl = $avatarUrl;
        $this->createdBy = $createdBy;
        $this->participantId = $participantId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-user.' . $this->participantId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'group.created';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'group_name' => $this->groupName,
            'avatar_url' => $this->avatarUrl,
            'created_by' => $this->createdBy,
        ];
    }
}
