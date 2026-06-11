<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class LikeHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $likeable = $this->likeable;
        $isDeleted = !$likeable || method_exists($likeable, 'trashed') && $likeable->trashed();

        $entityType = $isDeleted ? 'deleted' : class_basename($this->likeable_type);
        $postType = null;

        if (!$isDeleted && $likeable instanceof \App\Models\UserPost) {
            $postType = $likeable->type;
        }

        $preview = $this->buildPreview($likeable, $isDeleted);

        return [
            'id'          => $this->id,
            'entity_type' => $postType ?? $entityType,
            'entity_id'   => $this->likeable_id,
            'preview'     => $preview,
            'created_at'  => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),
        ];
    }

    private function buildPreview($likeable, bool $isDeleted): array
    {
        if ($isDeleted) {
            return [
                'caption'   => '[deleted]',
                'thumbnail' => null,
            ];
        }

        if ($likeable instanceof \App\Models\UserPost) {
            $media = $likeable->media->first();
            return [
                'caption'   => Str::limit($likeable->caption, 50),
                'thumbnail' => $media ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl()) : null,
            ];
        }

        return [
            'caption'   => Str::limit(method_exists($likeable, 'getCaption') ? $likeable->getCaption() : '', 50),
            'thumbnail' => null,
        ];
    }
}
