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

        // LOGIC: 'thumb' conversion (300x300) use karo, agar nahi hai to Original use karo.

        $thumbnailUrl = null;

        if ($this->type === 'video') {
            // SCENARIO 1: VIDEO
            // Kya Worker ne Thumbnail bana diya?
            if ($generatedThumb) {
                // Haan, to uska chhota version (thumb) uthao.
                // Note: Spatie generated images ke bhi conversions banata hai.
                $thumbnailUrl = $generatedThumb->hasGeneratedConversion('thumb')
                    ? $generatedThumb->getUrl('thumb')
                    : $generatedThumb->getUrl();
            } else {
                // Nahi, abhi worker chal raha hai. Video URL hi return kardo (Fallback).
                $thumbnailUrl = $mediaItem?->getUrl();
            }
        } else {
            // SCENARIO 2: IMAGE
            // Kya Image ka chhota version ready hai?
            $thumbnailUrl = ($mediaItem && $mediaItem->hasGeneratedConversion('thumb'))
                ? $mediaItem->getUrl('thumb')
                : $mediaItem?->getUrl();
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'caption' => $this->caption,
            'media_url' => $mediaItem?->getUrl(),

            // ✅ OPTIMIZED URL (Always 300x300 if ready)
            'thumbnail_url' => $thumbnailUrl,

            'mime_type' => $mediaItem?->mime_type,

            // ... baaki same ...
            'like_count' => $this->like_count,
            'view_count' => $this->views_count ?? 0,
            'is_liked' => (bool) ($this->is_liked ?? false),
            'is_viewed' => (bool) ($this->is_viewed ?? false),
            'is_mine' => $this->user_id === $user?->id,
            'expires_at' => $this->expires_at,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
