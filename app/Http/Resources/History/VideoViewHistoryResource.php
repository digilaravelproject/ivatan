<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class VideoViewHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $postType = $this->post_type ?? 'unknown';
        $viewable = $this->viewable;
        $isDeleted = !$viewable || method_exists($viewable, 'trashed') && $viewable->trashed();

        $preview = [
            'thumbnail' => null,
            'caption'   => $isDeleted ? '[deleted]' : Str::limit($this->post_caption ?? '', 50),
        ];

        if (!$isDeleted && $viewable instanceof \App\Models\UserPost) {
            $media = $viewable->media->first();
            $preview['thumbnail'] = $media ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl()) : null;
            $preview['caption'] = Str::limit($viewable->caption, 50);
        }

        return [
            'id'         => $this->id,
            'post_type'  => $postType,
            'entity_id'  => $this->viewable_id,
            'preview'    => $preview,
            'created_at' => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),
        ];
    }
}
