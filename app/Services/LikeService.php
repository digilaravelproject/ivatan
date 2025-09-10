<?php

namespace App\Services;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class LikeService
{
    public function like($model)
    {
        $user = Auth::user();

        $existingLike = $model->likes()->where('user_id', $user->id)->first();
        if ($existingLike) {
            throw new \Exception('You have already liked this item.');
        }

        $model->likes()->create([
            'user_id' => $user->id,
        ]);
        // ✅ Increment like_count if the column exists
        if ($this->hasLikeCountColumn($model)) {
            $model->increment('like_count');
        }
        return true;
    }

    public function unlike($model)
    {
        $user = Auth::user();

        $like = $model->likes()->where('user_id', $user->id)->first();
        if (!$like) {
            throw new \Exception('You have not liked this item yet.');
        }

        $like->delete();

        // ✅ Decrement like_count if the column exists
        if ($this->hasLikeCountColumn($model)) {
            $model->decrement('like_count');
        }
        return true;
    }

    public function hasLiked($model): bool
    {
        return $model->likes()->where('user_id', Auth::id())->exists();
    }

    public function likeCount($model): int
    {
        return $model->likes()->count();
    }
    protected function hasLikeCountColumn(Model $model): bool
    {
        return Schema::hasColumn($model->getTable(), 'like_count');
    }
}
