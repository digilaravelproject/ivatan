<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CommentService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function addComment(Model $model, array $data, ?string $parentId = null): Comment
    {
        $currentUser = Auth::user();

        // 1. Check block relation with the content owner
        $ownerId = $model->user_id ?? $model->seller_id ?? null;
        if ($ownerId) {
            $owner = User::find($ownerId);
            if ($owner && $currentUser && $currentUser->hasBlockRelationWith($owner)) {
                throw new \Exception('Action forbidden due to block status.', 403);
            }
        }

        // 2. Check block relation with the parent comment author if it is a reply
        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if ($parentComment && $parentComment->user) {
                if ($currentUser && $currentUser->hasBlockRelationWith($parentComment->user)) {
                    throw new \Exception('Action forbidden due to block status.', 403);
                }
            }
        }

        $comment = DB::transaction(function () use ($model, $data, $parentId) {
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

        // Notify content owner after transaction
        try {
            $ownerId = $model->user_id ?? null;
            if ($ownerId && (int) $ownerId !== (int) Auth::id()) {
                $owner = User::find($ownerId);
                if ($owner) {
                    $this->notificationService->sendToUser($owner, 'comment', [
                        'title'       => 'New Comment',
                        'message'     => Auth::user()->name . ' commented on your ' . class_basename($model),
                        'actor_id'    => Auth::id(),
                        'actor_name'  => Auth::user()->name,
                        'actor_avatar'=> Auth::user()->profile_photo_url,
                        'body'        => Str::limit($data['body'], 100),
                        'target_type' => get_class($model),
                        'target_id'   => $model->id,
                        'action_url'  => null,
                    ]);
                }
            }

            // If reply, also notify parent comment author
            if ($parentId) {
                $parentComment = Comment::find($parentId);
                if ($parentComment && (int) $parentComment->user_id !== (int) Auth::id()) {
                    $parentAuthor = User::find($parentComment->user_id);
                    if ($parentAuthor) {
                        $this->notificationService->sendToUser($parentAuthor, 'comment', [
                            'title'       => 'New Reply',
                            'message'     => Auth::user()->name . ' replied to your comment',
                            'actor_id'    => Auth::id(),
                            'actor_name'  => Auth::user()->name,
                            'actor_avatar'=> Auth::user()->profile_photo_url,
                            'body'        => Str::limit($data['body'], 100),
                            'target_type' => get_class($model),
                            'target_id'   => $model->id,
                            'action_url'  => null,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Comment notification failed', ['error' => $e->getMessage()]);
        }

        return $comment;
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
