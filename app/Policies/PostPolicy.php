<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPost;


class PostPolicy
{
    /**
     * Create a new policy instance.
     */
  public function view(?User $user, UserPost $post): bool
    {
        if ($post->visibility === 'public') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($post->user_id === $user->id) {
            return true;
        }

        if ($post->visibility === 'private') {
            // implement follow-check logic here (example placeholder)
            // return Follow::where('follower_id', $user->id)->where('followed_id', $post->user_id)->exists();
            return false;
        }

        if ($post->visibility === 'friends') {
            // custom logic for friends
            return false;
        }

        return false;
    }

    public function delete(User $user, UserPost $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole('admin');
    }

    public function update(User $user, UserPost $post): bool
    {
        return $user->id === $post->user_id;
    }
}
