<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $currentUser = Auth::guard('sanctum')->user();
        
        $isFollowed = false;
        if ($currentUser) {
            $isFollowed = $currentUser->isFollowing($this->resource);
        }

        $isAuthUser = $currentUser && ((int)$currentUser->id === (int)$this->id);

        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'avtar' => $this->profile_photo_url,
            'is_verified' => (bool) $this->is_verified,
            'is_followed_by_auth_user' => (bool) $isFollowed,
            'is_auth_user' => $isAuthUser,
        ];
    }
}
