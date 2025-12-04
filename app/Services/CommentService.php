<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CommentService
{
    /**
     * Create a comment via the relationship.
     * This fixes the Polymorphic Class Name mismatch.
     */
    public function addComment(Model $model, array $data, ?string $parentId = null): Comment
    {
        // 1. Create Comment using relationship
        $comment = $model->comments()->create([
            'user_id'   => Auth::id(),
            'body'      => $data['body'],
            'parent_id' => $parentId,
            'status'    => 'active',
        ]);

        // 2. Refresh parent stats immediately
        $this->updateCommentCount($model);

        return $comment;
    }

    /**
     * Delete comment and update parent stats.
     */
    public function deleteComment(Comment $comment): void
    {
        $commentable = $comment->commentable;

        // Delete replies first to trigger events if needed
        $comment->replies()->each(fn($reply) => $reply->delete());

        $comment->delete();

        // Refresh parent stats
        if ($commentable) {
            $this->updateCommentCount($commentable);
        }
    }

    /**
     * Map string types to Model Classes.
     */
    public function resolveModelClass(string $type): ?string
    {
        $map = [
            'post'     => 'App\\Models\\UserPost',
            'userpost' => 'App\\Models\\UserPost',
            'video'    => 'App\\Models\\Video',
            'product'  => 'App\\Models\\Product',
        ];

        $lowerType = strtolower($type);

        // Return mapped class or check if class exists directly
        return $map[$lowerType] ?? (class_exists('App\\Models\\' . Str::studly($type))
            ? 'App\\Models\\' . Str::studly($type)
            : null);
    }

    /**
     * Update comment_count column on parent model if it exists.
     */
    protected function updateCommentCount(Model $model): void
    {
        if (Schema::hasColumn($model->getTable(), 'comment_count')) {
            // Count directly from DB for accuracy
            $count = $model->comments()->count();
            $model->updateQuietly(['comment_count' => $count]); // updateQuietly avoids re-triggering timestamps if not needed
        }
    }
}
