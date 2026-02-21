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

        // Thumbnail Logic
        $thumbnailUrl = null;
        if ($this->type === 'video') {
            if ($generatedThumb) {
                $thumbnailUrl = $generatedThumb->hasGeneratedConversion('thumb') 
                    ? $generatedThumb->getUrl('thumb') 
                    : $generatedThumb->getUrl();
            } else {
                $thumbnailUrl = $mediaItem?->getUrl();
            }
        } else {
            $thumbnailUrl = ($mediaItem && $mediaItem->hasGeneratedConversion('thumb'))
                ? $mediaItem->getUrl('thumb') 
                : $mediaItem?->getUrl();
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'caption' => $this->caption,
            'media_url' => $mediaItem?->getUrl(),
            'thumbnail_url' => $thumbnailUrl,
            'mime_type' => $mediaItem?->mime_type,
            
            'like_count' => $this->like_count,
            'view_count' => $this->views_count ?? 0,
            
            // ✅ Count Sahi Karein
            // 'comment_count' => (int) ($this->comments_count ?? 0),

            // // ✅ Comments Array (Safe Mode)
            // // Agar comments load nahi huye, toh empty array jayega. Error nahi aayega.
            // 'comments' => $this->comments ? $this->comments->map(function($comment) {
            //     return [
            //         'id' => $comment->id,
            //         'body' => $comment->body, // Make sure DB me column name 'body' hi ho
            //         'created_human' => $comment->created_at->diffForHumans(),
            //         'user' => [
            //             'id' => $comment->user->id ?? null,
            //             'name' => $comment->user->name ?? 'User',
            //             'username' => $comment->user->username ?? '',
            //             'avatar' => $comment->user->avatar ?? null,
            //         ]
            //     ];
            // }) : [],

            'is_liked' => (bool) ($this->is_liked ?? false),
            'is_viewed' => (bool) ($this->is_viewed ?? false),
            'is_mine' => $this->user_id === $user?->id,
            'expires_at' => $this->expires_at,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}