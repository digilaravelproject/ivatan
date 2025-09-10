<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryHighlightResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover_media_id' => $this->cover_media_id,
            'stories' => StoryResource::collection($this->whenLoaded('stories')),
            'created_at' => $this->created_at,
        ];
    }
}
