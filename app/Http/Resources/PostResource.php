<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {

        // 1. Logged-in User
        // FIX: Added DocBlock to help IDE recognize User model
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
                // Intelephense now knows $authUser is a User model
                $isFollowing = $authUser->isFollowing($author);
            }
        }

        // ✅ LOGIC 3: INTERESTS STRING
        $interestsString = "";

        // Check if 'user.interests' relationship is loaded
        if ($author && $author->relationLoaded('interests')) {
            // Use 'getRelation' to avoid potential naming conflicts with column values
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
                'occupation' => $author->occupation,
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
                'share_count' => 0, // Placeholder for future expansion
                'view_count' => $this->view_count,
                'is_liked' => isset($this->likes_exists) ? (bool)$this->likes_exists : ($authUser ? (bool) $this->likes()->where('user_id', $authUser->id)->exists() : false),
                'is_saved' => false,
            ],

            // 5. Timestamps
            'created_at' => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),
        ];
    }
}
