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
        $generatedThumb = $this->getFirstMedia('thumbnail');
        $user = Auth::user();

        return [
            'id' => $this->id,
            'type' => $this->type,
            'caption' => $this->caption,
            'media_url' => $mediaItem?->getUrl(),
            // Only Video needs specific thumb check. Images use the main URL or conversions.
            'thumbnail_url' => ($this->type === 'video')
                ? ($generatedThumb?->getUrl() ?? $mediaItem?->getUrl())
                : $mediaItem?->getUrl(),
            'mime_type' => $mediaItem?->mime_type,

            // Engagement
            'like_count' => $this->like_count,
            'view_count' => $this->views_count ?? 0,

            // N+1 FIXED: Use boolean attributes loaded by Service
            'is_liked' => (bool) ($this->is_liked ?? false),
            'is_viewed' => (bool) ($this->is_viewed ?? false),
            'is_mine' => $this->user_id === $user?->id,

            // Timestamps
            'expires_at' => $this->expires_at,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
