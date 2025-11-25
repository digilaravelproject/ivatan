<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            // 1. Post Identity
            'id' => $this->id,
            'uuid' => $this->uuid, // UUID zaroori hai agar tum use karte ho
            'type' => $this->type, // post, video, reel
            'caption' => $this->caption,
            'visibility' => $this->visibility,

            // 2. Author Details (Grouped for easier component usage)
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'avatar' => $this->user->profile_photo_url, // Wahi logic jo pichli chat me fix kiya tha
                'is_verified' => $this->user->is_verified ?? false, // Agar verified tick dikhana ho
            ],

            // 3. Media Collection (Clean List)
            'media' => $this->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => str_starts_with($m->mime_type, 'video') ? 'video' : 'image',
                    'url' => $m->getUrl(),
                    // Agar video hai to thumb, agar image hai to original hi thumb banega
                    'thumbnail' => $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl(),
                    'mime_type' => $m->mime_type,
                    'aspect_ratio' => $m->getCustomProperty('aspect_ratio', null), // Agar store karte ho
                ];
            }),

            // 4. Engagement Stats (Frontend logic ke liye best)
            'stats' => [
                'like_count' => $this->like_count,
                'comment_count' => $this->comment_count,
                'view_count' => $this->view_count,
                // Yeh check karega ki logged-in user ne like kiya hai ya nahi
                'is_liked' => Auth::check() ? (bool) $this->likes()->where('user_id', Auth::id())->exists() : false,
                'is_saved' => false, // Future me agar save feature add karo
            ],

            // 5. Timestamps
            'created_at' => $this->created_at->toIso8601String(), // Machine readable
            'created_human' => $this->created_at->diffForHumans(), // "2 hours ago"
        ];
    }
}
