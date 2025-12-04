<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // ✅ LOGIC: INTERESTS STRING (Inspired by PostResource)
        $interestsString = "";
        $resource = $this->resource;

        // 1. Priority: Check if Controller eager loaded 'interests' (Best Performance)
        // Using getRelation() avoids conflict with 'interests' column containing IDs
        if ($resource->relationLoaded('interests')) {
            $interestsCollection = $resource->getRelation('interests');

            $interestsString = collect($interestsCollection)
                ->pluck('name')
                ->implode(', ');
        }
        // 2. Fallback: If not loaded, fetch manually (Fixes "Showing IDs" issue)
        else {
            try {
                // Force call relationship method () to bypass column data [1,2]
                $interestsString = $resource->interests()->pluck('name')->implode(', ');
            } catch (\Exception $e) {
                // If relationship doesn't exist or fails
                $interestsString = "";
            }
        }

        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'occupation' => $this->occupation,
            // Mapping 'profile_photo_path' to 'avtar' as requested
            'avtar' => $this->profile_photo_path
                ? asset('storage/' . $this->profile_photo_path)
                : null,
            'is_seller' => (bool) $this->is_seller,
            'is_verified' => (bool) $this->is_verified,
            'status' => $this->status,
            'followers_count' => (int) $this->followers_count,
            'following_count' => (int) $this->following_count,

            // ✅ Interest String output
            'interests' => $interestsString,
        ];
    }
}
