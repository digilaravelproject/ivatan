<?php

namespace App\Events;

use App\Models\Like;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentLiked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Like $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    public function broadcastOn(): array
    {
        if (!$this->like->comment || !$this->like->comment->user_id) {
            return [];
        }
        return [
            new PrivateChannel('user.' . $this->like->comment->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'like_id' => $this->like->id,
            'comment_id' => $this->like->comment_id,
            'liker_id' => $this->like->user_id,
            'liker_name' => $this->like->user?->name,
            'created_at' => $this->like->created_at?->toISOString(),
        ];
    }
}
