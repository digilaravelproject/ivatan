<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {

        // 1. Logged-in User
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::guard('sanctum')->user() ?? Auth::user();

        // Post Author (may be null if soft-deleted)
        /** @var \App\Models\User|null $author */
        $author = $this->user;

        // Check if author is deleted (soft-deleted)
        $isDeletedUser = !$author || ($author && $author->trashed());

        // ✅ LOGIC 1: IS MINE
        $isMine = false;
        if ($authUser && $author && !$isDeletedUser) {
            $isMine = $authUser->id === $author->id;
        }

        // ✅ LOGIC: IS BLOCKED (bidirectional check)
        $isBlocked = false;
        if ($authUser && $author && !$isMine && !$isDeletedUser) {
            $isBlocked = $authUser->hasBlockRelationWith($author);
        }

        // ✅ LOGIC 2: IS FOLLOWING
        $isFollowing = false;
        if ($authUser && $author && !$isDeletedUser) {
            if ($isMine) {
                $isFollowing = true;
            } else {
                $isFollowing = (bool)($author->is_followed_by_me ?? $authUser->isFollowing($author));
            }
        }

        // ✅ LOGIC 3: INTERESTS STRING
        $interestsString = "";
        if ($author && !$isDeletedUser && $author->relationLoaded('interests')) {
            $interestsCollection = $author->getRelation('interests');
            $interestsString = collect($interestsCollection)
                ->pluck('name')
                ->implode(', ');
        }

        // Author data - show "Deleted User" placeholder if author is soft-deleted
        $authorData = $isDeletedUser ? [
            'id' => null,
            'name' => 'Deleted User',
            'username' => 'deleted_user',
            'occupation' => null,
            'avatar' => 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128',
            'is_verified' => false,
            'interests' => '',
            'is_deleted_user' => true,
        ] : [
            'id' => $author->id,
            'name' => $author->name,
            'username' => $author->username,
            'occupation' => $author->occupation,
            'avatar' => $author->profile_photo_url,
            'is_verified' => $author->is_verified ?? false,
            'interests' => $interestsString,
        ];

        // ✅ LOGIC 4: EXCLUSIVE CONTENT & PURCHASE STATUS
        $hasAccess = true;
        if ($this->is_exclusive) {
            if (!$authUser) {
                $hasAccess = false;
            } elseif ($isMine) {
                $hasAccess = true;
            } elseif ($authUser->is_admin) {
                $hasAccess = true;
            } else {
                $hasAccess = (bool)$authUser->hasExclusiveAccessTo($this->id);
            }
        }

        $purchaseStatus = null;
        if ($authUser && $this->is_exclusive) {
            $purchase = \App\Models\ExclusiveContentPurchase::where('buyer_id', $authUser->id)
                ->where('user_post_id', $this->id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($purchase) {
                $purchaseStatus = $purchase->status; // 'pending', 'completed', 'failed'
            }
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
            'user' => $authorData,

            // 3. Media Collection - Secure URLs if exclusive and locked
            'media' => $this->media->map(function ($m) use ($hasAccess) {
                return [
                    'id' => $m->id,
                    'type' => str_starts_with($m->mime_type, 'video') ? 'video' : 'image',
                    'url' => $this->is_exclusive 
                        ? ($hasAccess ? $m->getUrl() : null)
                        : $m->getUrl(),
                    'thumbnail' => $this->is_exclusive 
                        ? ($hasAccess ? ($m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl()) : null) 
                        : ($m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl()),
                    'mime_type' => $m->mime_type,
                    'aspect_ratio' => $m->getCustomProperty('aspect_ratio', null),
                ];
            }),

            // 4. Stats
            'stats' => [
                'like_count' => $this->like_count,
                'comment_count' => $this->comment_count,
                'share_count' => 0, // Placeholder
                'view_count' => $this->view_count,
                'is_liked' => isset($this->likes_exists) ? (bool)$this->likes_exists : ($authUser ? (bool) $this->likes()->where('user_id', $authUser->id)->exists() : false),
                'is_saved' => isset($this->bookmarks_exists) ? (bool)$this->bookmarks_exists : ($authUser ? (bool) $this->bookmarks()->where('user_id', $authUser->id)->exists() : false),
                'is_blocked' => $isBlocked,
            ],

            // 5. Timestamps
            'created_at' => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),

            // 6. Exclusive Content Fields (Added at the end to keep existing fields intact)
            'is_exclusive' => (bool)$this->is_exclusive,
            'price' => $this->is_exclusive ? (float)$this->price : null,
            'exclusive_status' => $this->exclusive_status,
            'has_access' => $hasAccess,
            'purchase_status' => $purchaseStatus,
        ];
    }
}
