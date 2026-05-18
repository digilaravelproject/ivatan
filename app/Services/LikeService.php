<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class LikeService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function like(Model $model): bool
    {
        $ownerId = $model->user_id ?? null;

        $result = DB::transaction(function () use ($model) {
            $locked = $model->newQuery()
                ->whereKey($model->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existing = $locked->likes()->where('user_id', Auth::id())->first();
            if ($existing) {
                throw new \Exception('You have already liked this item.');
            }

            $locked->likes()->create(['user_id' => Auth::id()]);

            if ($this->hasLikeCountColumn($locked)) {
                $locked->increment('like_count');
            }

            return true;
        });

        // Send notification after transaction succeeds (non-blocking)
        if ($ownerId && (int) $ownerId !== (int) Auth::id()) {
            try {
                $owner = User::find($ownerId);
                if ($owner) {
                    $this->notificationService->sendToUser($owner, 'like', [
                        'title'        => 'New Like',
                        'message'      => Auth::user()->name . ' liked your ' . class_basename($model),
                        'actor_id'     => Auth::id(),
                        'actor_name'   => Auth::user()->name,
                        'actor_avatar' => Auth::user()->profile_photo_url,
                        'target_type'  => get_class($model),
                        'target_id'    => $model->id,
                        'action_url'   => null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Like notification failed', ['error' => $e->getMessage()]);
            }
        }

        return $result;
    }

    public function unlike(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            $locked = $model->newQuery()
                ->whereKey($model->id)
                ->lockForUpdate()
                ->firstOrFail();

            $like = $locked->likes()->where('user_id', Auth::id())->first();
            if (!$like) {
                throw new \Exception('You have not liked this item yet.');
            }

            $like->delete();

            if ($this->hasLikeCountColumn($locked)) {
                $locked->decrement('like_count');
            }

            return true;
        });
    }

    public function hasLiked(Model $model): bool
    {
        return $model->likes()->where('user_id', Auth::id())->exists();
    }

    public function likeCount(Model $model): int
    {
        return $model->likes()->count();
    }

    protected function hasLikeCountColumn(Model $model): bool
    {
        return Schema::hasColumn($model->getTable(), 'like_count');
    }
}
