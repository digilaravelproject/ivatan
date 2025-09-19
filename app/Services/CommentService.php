<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    public function addComment(array $data): Comment
    {
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'body' => $data['body'],
            'commentable_type' => $data['commentable_type'],
            'commentable_id' => $data['commentable_id'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        // 🔄 Update comment_count if applicable
        $this->updateCommentCount($comment->commentable);

        return $comment;
    }

    public function deleteComment(Comment $comment): void
    {
        $commentable = $comment->commentable;

        // Also delete replies (if any)
        $comment->replies()->delete();

        $comment->delete();

        // ✅ Prevent null error
        if ($commentable) {
            $this->updateCommentCount($commentable);
        }
    }


    protected function updateCommentCount(Model $model): void
    {
        if ($this->hasCommentCountColumn($model)) {
            $model->update([
                'comment_count' => $model->comments()->count(),
            ]);
        }
    }

    protected function hasCommentCountColumn(Model $model): bool
    {
        return Schema::hasColumn($model->getTable(), 'comment_count');
    }
}
