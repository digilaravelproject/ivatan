<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mediaItem = $this->getFirstMedia('stories');
        $user = Auth::user();

        return [
            'id' => $this->id,
            'type' => $this->type,
            'caption' => $this->caption,
            'media_url' => $mediaItem?->getUrl(),
            'thumbnail_url' => ($this->type === 'video' && $mediaItem?->hasGeneratedConversion('thumb'))
                ? $mediaItem->getUrl('thumb')
                : $mediaItem?->getUrl(),
            'mime_type' => $mediaItem?->mime_type,

            // Engagement
            'like_count' => $this->like_count,
            'view_count' => $this->views_count ?? 0, // From Trait
            'is_liked' => $user ? $this->likes->contains($user->id) : false,
            'is_viewed' => $user ? $this->isViewedBy($user->id) : false,

            // Timestamps
            'expires_at' => $this->expires_at,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
