<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEdited implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chatId;
    public int $messageId;
    public string $newContent;
    public string $editedAt;

    public function __construct(int $chatId, int $messageId, string $newContent, string $editedAt)
    {
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->newContent = $newContent;
        $this->editedAt = $editedAt;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.edited';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'new_content' => $this->newContent,
            'edited_at' => $this->editedAt,
        ];
    }
}
