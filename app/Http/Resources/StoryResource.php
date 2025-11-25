<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoryResource extends JsonResource
{
    public function toArray($request): array
    {
        // Media retrieval optimize kiya hai
        $mediaItem = $this->getFirstMedia('stories');

        return [
            'id' => $this->id,
            'type' => $this->type, // image or video
            'caption' => $this->caption,
            'meta' => $this->meta,

            // Media URLs
            'media_url' => $mediaItem ? $mediaItem->getUrl() : null,
            'thumbnail_url' => ($this->type === 'video' && $mediaItem && $mediaItem->hasGeneratedConversion('thumb'))
                ? $mediaItem->getUrl('thumb')
                : ($mediaItem ? $mediaItem->getUrl() : null), // Fallback for image
            'mime_type' => $mediaItem ? $mediaItem->mime_type : null,

            // Engagement
            'like_count' => $this->like_count,
            'liked_by_me' => Auth::check() ? (bool) $this->likes()->where('user_id', Auth::id())->exists() : false,
            'view_count' => $this->views_count ?? 0, // Agar views count hai toh

            // User Details with Correct Avatar Logic
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                // Yeh tumhare User model ke accessor se aayega
                'avatar' => $this->user->profile_photo_url,
            ],

            // Timestamps
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at->diffForHumans(), // "2 hours ago" format looks better like Insta
            'created_at_raw' => $this->created_at,
        ];
    }
}
