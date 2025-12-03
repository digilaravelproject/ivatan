<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommentService
{
    /**
     * Add a new comment to the database.
     */
    public function addComment(array $data): Comment
    {
        $comment = Comment::create([
            'user_id'          => Auth::id(),
            'body'             => $data['body'],
            'commentable_type' => $data['commentable_type'],
            'commentable_id'   => $data['commentable_id'],
            'parent_id'        => $data['parent_id'] ?? null,
            'status'           => 'active', // Default status
        ]);

        // 🔄 Update parent model count (e.g. Post comment_count)
        if ($comment->commentable) {
            $this->updateCommentCount($comment->commentable);
        }

        return $comment;
    }

    /**
     * Delete a comment and its replies.
     */
    public function deleteComment(Comment $comment): void
    {
        $commentable = $comment->commentable;

        // Delete replies first (Standard eloquent delete triggers events)
        $comment->replies()->each(function ($reply) {
            $reply->delete();
        });

        $comment->delete();

        // Update count on the parent model
        if ($commentable) {
            $this->updateCommentCount($commentable);
        }
    }

    /**
     * Helper to resolve string type to Model Class.
     * e.g., 'UserPost' -> 'App\Models\UserPost'
     */
    public function resolveModelClass(string $type): ?string
    {
        // Map frontend friendly names to actual Models
        $map = [
            'post' => 'App\\Models\\UserPost',
            'userpost' => 'App\\Models\\UserPost',
            'video' => 'App\\Models\\Video',
            'product' => 'App\\Models\\Product',
        ];

        // Check map first (case insensitive)
        $lowerType = strtolower($type);
        if (array_key_exists($lowerType, $map)) {
            return $map[$lowerType];
        }

        // Fallback: Check if direct model path exists
        $directModel = 'App\\Models\\' . Str::studly($type);
        if (class_exists($directModel)) {
            return $directModel;
        }

        return null;
    }

    protected function updateCommentCount(Model $model): void
    {
        if (Schema::hasColumn($model->getTable(), 'comment_count')) {
            // Count all comments (parents + replies) related to this model
            $count = $model->comments()->count();
            $model->update(['comment_count' => $count]);
        }
    }
}
