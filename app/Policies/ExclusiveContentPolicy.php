<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPost;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExclusiveContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the exclusive content media.
     */
    public function viewMedia(User $user, UserPost $post): bool
    {
        // 1. Is it exclusive content?
        if (!$post->is_exclusive) {
            return true;
        }

        // 2. Is the user the creator?
        if ($user->id === $post->user_id) {
            return true;
        }

        // 3. Block Check: If the creator blocked the user or user blocked creator
        if ($user->hasBlockRelationWith($post->user)) {
            return false;
        }

        // 4. Admin check (optional, depending on business rules)
        if ($user->is_admin) {
            return true;
        }

        // 5. Does the user have active access?
        return $user->hasExclusiveAccessTo($post->id);
    }
}
