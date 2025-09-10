<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'caption' => $this->caption,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username ?? null,
            ],
            'media' => $this->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'url' => $m->getUrl(),
                    'thumb' => $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : null,
                    'mime_type' => $m->mime_type,
                    'meta' => $m->custom_properties,
                ];
            }),
            'like_count' => $this->like_count,
            'comment_count' => $this->comment_count,
            'created_at' => $this->created_at,
        ];
    }
}


// Use in controller: return new PostResource($post->load('media','user')); or for collections use PostResource::collection($posts).
