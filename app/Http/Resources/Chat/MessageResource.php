<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        // Handle "Delete for Everyone" logic
        if ($this->deleted_at) {
            return [
                'id' => $this->id,
                'chat_id' => $this->chat_id,
                'content' => 'This message was deleted',
                'message_type' => 'system', // or 'deleted'
                'is_deleted' => true,
                'is_mine' => $this->sender_id === $user->id,
                'created_at' => $this->created_at->toIso8601String(),
                'sender' => null, // Hide sender info for deleted msgs usually
            ];
        }

        return [
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'content' => $this->content,
            'message_type' => $this->message_type, // text, image, file, system
            'attachment_url' => $this->attachment_path ? Storage::url($this->attachment_path) : null,
            'meta' => $this->meta,
            'is_mine' => $this->sender_id === $user->id,
            'status' => $this->status_for_user, // sent, delivered, read (Calculated in Model)
            'created_at' => $this->created_at->toIso8601String(),
            'reply_to' => $this->whenLoaded('replyTo', function () {
                return [
                    'id' => $this->replyTo->id,
                    'content' => $this->replyTo->deleted_at ? 'Deleted message' : $this->replyTo->content,
                    'sender_name' => $this->replyTo->sender->name ?? 'Unknown'
                ];
            }),
            'sender' => ($this->sender && !$this->sender->trashed()) ? [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->profile_photo_url ?? null,
            ] : [
                'id' => null,
                'name' => 'Deleted User',
                'avatar' => 'https://ui-avatars.com/api/?name=Deleted+User&color=fff&background=999&size=128',
                'is_deleted_user' => true,
            ],
        ];
    }
}
