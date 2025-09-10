<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $media = $this->whenLoaded('media') ? $this->media->first() : $this->getFirstMedia('stories');

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username ?? null,
                'avatar' => $this->user->getFirstMediaUrl('avatars') ?? null,
            ],
            'caption' => $this->caption,
            'meta' => $this->meta,
            'type' => $this->type,
            'like_count' => $this->like_count,
            'liked_by_me' => auth()->check() ? $this->likes()->where('user_id', auth()->id())->exists() : false,
            'media' => $media ? [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
                'mime_type' => $media->mime_type,
                'custom_properties' => $media->custom_properties,
            ] : null,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}
