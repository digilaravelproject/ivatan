<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveChatGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'chat_mode'   => $this->chat_mode,
            'is_active'   => (bool) $this->is_active,
            'chat_id'     => $this->chat?->id,
            'created_by'  => $this->when($this->relationLoaded('creator') && $this->creator, function () {
                $creator = $this->creator;
                $isDeleted = $creator->trashed();
                return $isDeleted ? [
                    'id' => null,
                    'name' => 'Deleted User',
                    'is_deleted_user' => true,
                ] : [
                    'id'   => $creator->id,
                    'name' => $creator->name,
                ];
            }),
            'created_at'  => $this->created_at,
        ];

        if ($this->relationLoaded('chat') && $this->chat) {
            $participants = $this->chat
                ->participants()
                ->with(['user' => fn ($q) => $q->select(['id', 'name', 'username', 'profile_photo_path', 'deleted_at'])])
                ->select(['id', 'chat_id', 'user_id', 'is_admin', 'joined_at', 'is_banned', 'is_muted', 'muted_until'])
                ->orderBy('joined_at')
                ->paginate(50);

            $data['participants'] = ParticipantResource::collection($participants)->response()->getData(true);
        }

        return $data;
    }
}
