<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryHighlightResource extends JsonResource
{
    public function toArray($request): array
    {
        // Fetch URL: Try to get the 'cover' conversion (500x500 crop), fallback to original
        $coverUrl = $this->getFirstMediaUrl('cover_media', 'cover');

        // If conversion doesn't exist (e.g. queue not finished), get original
        if (! $coverUrl) {
            $coverUrl = $this->getFirstMediaUrl('cover_media');
        }

        return [
            'id' => $this->id,
            'title' => $this->title,

            // Existing ID field
            'cover_media_id' => $this->cover_media_id,

            // ✅ New: Cover URL added here
            'cover_media_url' => $coverUrl,

            'stories' => StoryResource::collection($this->whenLoaded('stories')),
            'created_at' => $this->created_at,
        ];
    }
}
