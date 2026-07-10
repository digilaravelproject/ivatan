<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait Blockable
{
    /**
     * Scope to filter out entities owned by or associated with blocked/blocking users.
     */
    public function scopeWithoutBlocked(Builder $query, ?User $viewer = null): Builder
    {
        $viewer = $viewer ?? auth('sanctum')->user();
        if (!$viewer) {
            return $query;
        }

        $blockedIds = $viewer->getAllBlockedIds();
        if (empty($blockedIds)) {
            return $query;
        }

        $model = $query->getModel();
        $class = get_class($model);
        $table = $model->getTable();

        // If we are querying the User model, exclude blocked users directly
        if ($class === User::class) {
            return $query->whereNotIn("{$table}.id", $blockedIds);
        }

        // If we are querying UserChats (Direct Chats / DMs)
        if ($class === \App\Models\Chat\UserChat::class) {
            return $query->whereDoesntHave('participants', function ($q) use ($blockedIds) {
                $q->whereIn('user_id', $blockedIds);
            });
        }

        // Check for commonly used user foreign key columns
        if (Schema::hasColumn($table, 'user_id')) {
            return $query->whereNotIn("{$table}.user_id", $blockedIds);
        }

        if (Schema::hasColumn($table, 'sender_id')) {
            return $query->whereNotIn("{$table}.sender_id", $blockedIds);
        }

        return $query;
    }
}
