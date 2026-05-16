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

        return [
            'id'            => $this->id,
            'body'          => $this->body,
            'entity_type'   => $entityType,
            'entity_id'     => $this->commentable_id,
            'parent_id'     => $this->parent_id,
            'created_at'    => $this->created_at->toIso8601String(),
            'created_human' => $this->created_at->diffForHumans(),
        ];
    }
}
