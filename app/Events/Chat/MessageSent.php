<?php

namespace App\Events\Chat;

use App\Models\Chat\UserChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserChatMessage $message;

    public function __construct(UserChatMessage $message)
    {
        // Load sender details to avoid extra API calls on frontend when message arrives
        $this->message = $message->load('sender:id,name,username,profile_photo_path');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->chat_id),
        ];
    }

    /**
     * The event's broadcast name.
     * Adding a dot (.) at the start allows listening via '.message.sent'
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'uuid'            => $this->message->uuid,
            'chat_id'         => $this->message->chat_id,
            'sender'          => [
                'id'                 => $this->message->sender->id,
                'name'               => $this->message->sender->name,
                'username'           => $this->message->sender->username,
                'profile_photo_path' => $this->message->sender->profile_photo_path,
            ],
            'content'         => $this->message->content,
            'message_type'    => $this->message->message_type,
            'attachment_path' => $this->message->attachment_path ? url('/storage/' . $this->message->attachment_path) : null,
            'meta'            => $this->message->meta,
            'reply_to_id'     => $this->message->reply_to_message_id,
            'created_at'      => $this->message->created_at->toISOString(),
            'status'          => 'sent', // Default status for real-time
        ];
    }
}
