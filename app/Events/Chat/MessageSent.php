<?php

namespace App\Events\Chat;

use App\Models\Chat\UserChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserChatMessage $message;

    public function __construct(UserChatMessage $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('presence-chat.' . $this->message->chat_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'content' => $this->message->content,
            'message_type' => $this->message->message_type,
            'attachment_url' => $this->message->attachment_path ? url('/storage/' . $this->message->attachment_path) : null,
            'is_mine' => false,
            'status' => 'sent',
            'created_at' => $this->message->created_at->toISOString(),
            'sender' => $this->message->sender ? [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'avatar' => $this->message->sender->profile_photo_url,
            ] : null,
            'reply_to_id' => $this->message->reply_to_message_id,
        ];
    }
}
