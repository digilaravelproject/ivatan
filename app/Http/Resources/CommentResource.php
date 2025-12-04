<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth; // Auth facade import karein

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Current logged in user ka ID nikalo (safe check ke sath)
        $currentUserId = Auth::guard('web')->id() ?? Auth::id();

        return [
            'id' => $this->id,
            'commentable_type' => class_basename($this->commentable_type),
            'commentable_id' => $this->commentable_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'status' => $this->status,

            // Counts
            'likes_count' => (int) ($this->likes_count ?? 0),
            'total_reply_count' => (int) ($this->replies_count ?? 0),

            // User Interaction
            // Yahan check kar rahe hain agar user logged in hai aur ID match hoti hai
            'is_mine' => $currentUserId && ($this->user_id === $currentUserId),

            'has_liked' => (bool) $this->has_liked,

            // Timestamps
            'created_at' => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),

            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),

            // Recursive nesting for replies (Isme bhi automatic is_mine true/false ho jayega)
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
