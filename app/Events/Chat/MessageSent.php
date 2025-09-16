<?php

namespace App\Events\Chat;

use App\Models\Chat\UserChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public UserChatMessage $message;
    /**
     * Create a new event instance.
     */
    public function __construct(UserChatMessage $message)
    {
        $this->message = $message->load('sender');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
         \Log::info("Broadcasting on chat.{$this->message->chat_id}");
        return [
            new PrivateChannel('chat.' . $this->message->chat_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'uuid' => $this->message->uuid,
            'chat_id' => $this->message->chat_id,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
            ],
            'content' => $this->message->content,
            'message_type' => $this->message->message_type,
            'attachment_path' => $this->message->attachment_path ? url('/storage/' . $this->message->attachment_path) : null,
            'meta' => $this->message->meta,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
