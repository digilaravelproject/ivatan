<?php

namespace App\Services;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class LikeService
{
    public function like(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
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
