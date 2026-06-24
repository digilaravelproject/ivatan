<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isPrivate = $this->type === 'private';

        // Private Chat: Name/Avatar is the other person
        // Group Chat: Name/Avatar is the Group Name/Image
        $name = $this->name;
        $avatar = $this->avatar_url; // Assuming accessor in Model
        $isOnline = false;
        $otherUser = null;

        if ($isPrivate) {
            $otherParticipant = $this->participants->firstWhere('user_id', '!=', $user->id);
            if ($otherParticipant) {
                $otherUser = $otherParticipant->user;
                $isDeleted = !$otherUser || $otherUser->trashed();
                $name = $isDeleted ? 'Deleted User' : $otherUser->name;
                $avatar = $isDeleted
                    ? 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128'
                    : $otherUser->profile_photo_url;
                $isOnline = $isDeleted ? false : $otherUser->is_online;
            }
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'type' => $this->type, // private, group
            'name' => $name,
            'avatar' => $avatar,
            'is_online' => $isOnline,
            'is_admin' => $this->participants->firstWhere('user_id', $user->id)->is_admin ?? false,

            // Unread Count Logic
            'unread_count' => (int) ($this->unread_count ?? (function() use ($user) {
                $participant = $this->participants->firstWhere('user_id', $user->id);
                return $participant 
                    ? $this->messages()
                        ->where('id', '>', $participant->last_read_message_id ?? 0)
                        ->where('sender_id', '!=', $user->id)
                        ->count()
                    : 0;
            })()),

            // Last Message
            'last_message' => $this->lastMessage ? new MessageResource($this->lastMessage) : null,
            'updated_at' => $this->last_message_at ?? $this->updated_at,

            // Group Specifics
            'participants_count' => $this->participants_count, // Use withCount in query
            'participants' => $this->when($this->relationLoaded('participants'), function () {
                // Return simplified participant list if loaded
                return $this->participants->map(function ($p) {
                    $user = $p->user;
                    $isDeleted = !$user || $user->trashed();
                    return [
                        'user_id' => $isDeleted ? null : $p->user_id,
                        'name' => $isDeleted ? 'Deleted User' : $user->name,
                        'avatar' => $isDeleted
                            ? 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128'
                            : $user->profile_photo_url,
                        'is_admin' => (bool) $p->is_admin,
                        'is_deleted_user' => $isDeleted,
                    ];
                });
            }),
        ];
    }
}
