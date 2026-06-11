<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CommentHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $commentable = $this->commentable;
        $isDeleted = !$commentable || method_exists($commentable, 'trashed') && $commentable->trashed();

        $entityType = $isDeleted ? 'deleted' : class_basename($this->commentable_type ?? '');
        $postType = null;

        if (!$isDeleted && $commentable instanceof \App\Models\UserPost) {
            $postType = $commentable->type;
        }

        $preview = $this->buildPreview($commentable, $isDeleted);

        return [
            'id'            => $this->id,
            'body'          => $this->body,
            'entity_type'   => $postType ?? $entityType,
            'entity_id'     => $this->commentable_id,
            'parent_id'     => $this->parent_id,
            'preview'       => $preview,
            'created_at'    => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),
        ];
    }

    private function buildPreview($commentable, bool $isDeleted): array
    {
        if ($isDeleted) {
            return [
                'caption'   => '[deleted]',
                'thumbnail' => null,
            ];
        }

        if ($commentable instanceof \App\Models\UserPost) {
            $media = $commentable->media->first();
            return [
                'caption'   => Str::limit($commentable->caption, 50),
                'thumbnail' => $media ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl()) : null,
            ];
        }

        return [
            'caption'   => Str::limit(method_exists($commentable, 'getCaption') ? $commentable->getCaption() : '', 50),
            'thumbnail' => null,
        ];
    }
}
