<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryFeedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this refers to the User model here
        return [
            'user' => [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
                'avatar' => $this->profile_photo_url,
                'is_verified' => $this->is_verified ?? false,
            ],
            // Return only active stories for this user
            'stories' => StoryResource::collection($this->stories),
            'has_unseen' => $this->stories->contains(fn($story) => !$story->is_viewed),
        ];
    }
}
