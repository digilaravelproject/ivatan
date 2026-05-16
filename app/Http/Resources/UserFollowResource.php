<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserFollowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Get the avatar URL from the database path
        $avtarUrl = $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;

        // The property 'is_followed_by_auth_user' is added via DB::raw in the controller
        $isFollowed = $this->resource->getAttribute('is_followed_by_auth_user') ?? false;
        /*
            * Determine if the current authenticated user is the same as this user
            */
        $isAuthUser = Auth::check() && ((int)Auth::id() === (int)$this->id);


        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'avtar' => $avtarUrl,
            'is_verified' => (bool) $this->is_verified,

            // Follow status relative to the authenticated user
            'is_followed_by_auth_user' => (bool) $isFollowed,
            'is_auth_user' => $isAuthUser,
        ];
    }
}
