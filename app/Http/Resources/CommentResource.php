<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'commentable_type' => class_basename($this->commentable_type), // Returns "UserPost" instead of "App\Models\UserPost"
            'commentable_id' => $this->commentable_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'status' => $this->status,

            // Counts
            'likes_count' => (int) ($this->likes_count ?? 0),
            'total_reply_count' => (int) ($this->replies_count ?? 0), // Uses withCount('replies')

            // User Interaction
            'has_liked' => (bool) $this->has_liked,

            // Timestamps (Formatted for humans if needed, or ISO)
            'created_at' => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(), // "2 mins ago" - Instagram style

            // Relationships using the UserResource we created
            'user' => new UserResource($this->whenLoaded('user')),

            // Recursive nesting for replies
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
