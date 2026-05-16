<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPost;


class UserPostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(?User $user, UserPost $post): bool
    {
        // Admins can see everything
        if ($user && $user->is_admin) {
            return true; // Admins can see any post
        }

        // A user can always view their own posts
        if ($post->user_id === $user->id) {
            return true; // Post owner can always view their own posts
        }

        // For public posts, anyone can view
        if ($post->visibility === 'public') {
            return true; // Public posts are accessible to everyone
        }

        // If the user is not logged in, they can't view private or friends-only posts
        if (!$user) {
            return false; // No logged-in user
        }

        // Handle 'private' visibility: Only followers can see it
        if ($post->visibility === 'private') {
            // Check if the user is following the post owner
            return $user->following()->where('following_id', $post->user_id)->exists();
        }

        // Handle 'friends' visibility: Only people who follow each other can see the post
        if ($post->visibility === 'friends') {
            // Check if the user follows the post owner AND the post owner follows the user back
            $isFollowingUser = $user->following()->where('following_id', $post->user_id)->exists();
            $isFollowedByUser = $post->user->following()->where('following_id', $user->id)->exists();

            return $isFollowingUser && $isFollowedByUser;
        }

        // Default return false if no conditions match
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
