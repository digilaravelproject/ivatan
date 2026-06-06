<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryFeedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isDeletedUser = $this->trashed();

        return [
            'user' => $isDeletedUser ? [
                'user_id' => null,
                'username' => 'deleted_user',
                'name' => 'Deleted User',
                'avatar' => 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128',
                'is_verified' => false,
                'is_deleted_user' => true,
            ] : [
                'user_id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'avatar' => $this->profile_photo_url,
                'is_verified' => $this->is_verified ?? false,
            ],
            'stories' => StoryResource::collection($this->stories),
            // Check if any story is NOT viewed yet (using boolean flag)
            'has_unseen' => $this->stories->contains(fn($story) => !($story->is_viewed ?? false)),
        ];
    }
}
