<?php

namespace App\Events\Chat;

use App\Models\Chat\UserChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
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
        $channels = [
            new PresenceChannel('presence-chat.' . $this->message->chat_id),
        ];

        // Broadcast to all participants' private-user channels (except sender)
        $participants = \App\Models\Chat\UserChatParticipant::where('chat_id', $this->message->chat_id)
            ->where('user_id', '!=', $this->message->sender_id)
            ->pluck('user_id');

        foreach ($participants as $userId) {
            $channels[] = new PrivateChannel('private-user.' . $userId);
        }

        return $channels;
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
            'status' => $this->message->statusForUser($this->message->sender_id),
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
