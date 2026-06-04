<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->when($this->relationLoaded('user') && $this->user, [
                'id'       => $this->user->id,
                'name'     => $this->user->name,
                'username' => $this->user->username,
                'avatar'   => $this->user->profile_photo_url,
            ]),
            'role'       => $this->is_admin ? 'admin' : 'member',
            'joined_at'  => $this->joined_at,
            'is_banned'  => (bool) $this->is_banned,
            'is_muted'   => (bool) $this->is_muted,
            'muted_until' => $this->muted_until,
        ];
    }
}
