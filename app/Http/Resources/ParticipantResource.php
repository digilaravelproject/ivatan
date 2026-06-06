<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('user');
        $isDeletedUser = $user && ($user->trashed ?? false);

        return [
            'id' => $this->id,
            'user' => $user ? ($isDeletedUser ? [
                'id'       => null,
                'name'     => 'Deleted User',
                'username' => 'deleted_user',
                'avatar'   => 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128',
                'is_deleted_user' => true,
            ] : [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'avatar'   => $user->profile_photo_url,
            ]) : null,
            'role'       => $this->is_admin ? 'admin' : 'member',
            'joined_at'  => $this->joined_at,
            'is_banned'  => (bool) $this->is_banned,
            'is_muted'   => (bool) $this->is_muted,
            'muted_until' => $this->muted_until,
        ];
    }
}
