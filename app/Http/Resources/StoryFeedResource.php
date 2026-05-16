<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryFeedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
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
