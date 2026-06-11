<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function broadcastOn(): array
    {
        if (!$this->comment->post || !$this->comment->post->user_id) {
            return [];
        }
        return [
            new PrivateChannel('user.' . $this->comment->post->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'commenter_id' => $this->comment->user_id,
            'commenter_name' => $this->comment->user?->name,
            'content' => $this->comment->content,
            'created_at' => $this->comment->created_at?->toISOString(),
        ];
    }
}
