<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        // 1. Logged-in User
        // ✅ FIX: DocBlock add kiya taaki IDE ko pata chale ye App\Models\User hai
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::guard('sanctum')->user() ?? Auth::user();

        // Post Author
        /** @var \App\Models\User $author */
        $author = $this->user;

        // ✅ LOGIC 1: IS MINE
        $isMine = false;
        if ($authUser && $author) {
            $isMine = $authUser->id === $author->id;
        }

        // ✅ LOGIC 2: IS FOLLOWING
        $isFollowing = false;
        if ($authUser) {
            if ($isMine) {
                $isFollowing = true;
            } else {
                // Ab Intelephense error nahi dega kyunki use pata hai $authUser User model hai
                $isFollowing = $authUser->isFollowing($author);
            }
        }

        // ✅ LOGIC 3: INTERESTS STRING
        $interestsString = "";

        // Check karo ki Controller ne 'user.interests' load kiya hai ya nahi
        if ($author && $author->relationLoaded('interests')) {
            // 'getRelation' use karo taaki column value se conflict na ho
            $interestsCollection = $author->getRelation('interests');

            $interestsString = collect($interestsCollection)
                ->pluck('name')
                ->implode(', ');
        }

        return [
            // 1. Post Identity
            'id' => $this->id,
            'uuid' => $this->uuid,
            'type' => $this->type,
            'caption' => $this->caption,
            'visibility' => $this->visibility,

            // New Context Flags
            'is_mine' => $isMine,
            'is_following' => $isFollowing,

            // 2. Author Details
            'user' => [
                'id' => $author->id,
                'name' => $author->name,
                'username' => $author->username,
                'avatar' => $author->profile_photo_url,
                'is_verified' => $author->is_verified ?? false,

                // ✅ Ab ye 100% chalega
                'interests' => $interestsString,
            ],

            // 3. Media Collection
            'media' => $this->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => str_starts_with($m->mime_type, 'video') ? 'video' : 'image',
                    'url' => $m->getUrl(),
                    'thumbnail' => $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl(),
                    'mime_type' => $m->mime_type,
                    'aspect_ratio' => $m->getCustomProperty('aspect_ratio', null),
                ];
            }),

            // 4. Stats
            'stats' => [
                'like_count' => $this->like_count,
                'comment_count' => $this->comment_count,
                'view_count' => $this->view_count,
                'is_liked' => $authUser ? (bool) $this->likes()->where('user_id', $authUser->id)->exists() : false,
                'is_saved' => false,
            ],

            // 5. Timestamps
            'created_at' => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),
        ];
    }
}
