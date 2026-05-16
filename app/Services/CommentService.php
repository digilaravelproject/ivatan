<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CommentService
{
    public function addComment(Model $model, array $data, ?string $parentId = null): Comment
    {
        return DB::transaction(function () use ($model, $data, $parentId) {
            $locked = $model->newQuery()
                ->whereKey($model->id)
                ->lockForUpdate()
                ->firstOrFail();

            $comment = $locked->comments()->create([
                'user_id'   => Auth::id(),
                'body'      => $data['body'],
                'parent_id' => $parentId,
                'status'    => 'active',
            ]);

            $this->updateCommentCount($locked);

            return $comment;
        });
    }

    public function deleteComment(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $commentable = $comment->commentable;

            if ($commentable) {
                $locked = $commentable->newQuery()
                    ->whereKey($commentable->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $comment->replies()->each(fn($reply) => $reply->delete());
            $comment->delete();

            if (isset($locked)) {
                $this->updateCommentCount($locked);
            }
        });
    }

    public function resolveModelClass(string $type): ?string
    {
        $map = [
            'post'     => 'App\\Models\\UserPost',
            'userpost' => 'App\\Models\\UserPost',
            'story'    => 'App\\Models\\UserStory',
            'product'  => 'App\\Models\\Product',
        ];

        $lowerType = strtolower($type);

        return $map[$lowerType] ?? (class_exists('App\\Models\\' . Str::studly($type))
            ? 'App\\Models\\' . Str::studly($type)
            : null);
    }

    protected function updateCommentCount(Model $model): void
    {
        if (Schema::hasColumn($model->getTable(), 'comment_count')) {
            $count = $model->comments()->count();
            $model->updateQuietly(['comment_count' => $count]);
        }
    }
}
