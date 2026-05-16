<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait VisibilityTrait
{
    /**
     * Common function to filter posts or reels based on visibility.
     */
    private function scopeVisible(Builder $query, $user, $type)
    {
        return $query->where(function ($query) use ($user, $type) {
            // Public posts (visible to everyone)
            $query->where('visibility', 'public')
                ->where('type', $type);

            if ($user) {
                // Private posts (visible to followers)
                $query->orWhere(function ($query) use ($user, $type) {
                    $query->where('visibility', 'private')
                        ->where('type', $type)
                        ->whereExists(function ($q) use ($user) {
                            $q->selectRaw(1)
                                ->from('followers')
                                ->where('followers.following_id', '=', 'user_posts.user_id')
                                ->where('followers.follower_id', '=', $user->id);
                        });
                });

                // Friends posts (mutual followers)
                $query->orWhere(function ($query) use ($user, $type) {
                    $query->where('visibility', 'friends')
                        ->where('type', $type)
                        ->whereExists(function ($q) use ($user) {
                            $q->selectRaw(1)
                                ->from('followers')
                                ->where('followers.following_id', '=', 'user_posts.user_id')
                                ->where('followers.follower_id', '=', $user->id);
                        })
                        ->whereExists(function ($q) use ($user) {
                            $q->selectRaw(1)
                                ->from('followers')
                                ->where('followers.follower_id', '=', 'user_posts.user_id')
                                ->where('followers.following_id', '=', $user->id);
                        });
                });

                // User's own posts (regardless of visibility)
                $query->orWhere(function ($query) use ($user, $type) {
                    $query->where('user_posts.user_id', '=', $user->id)
                        ->where('type', $type);
                });
            }
        });
    }

    /**
     * Scope to filter posts based on visibility excluding reels.
     */
    public function scopeVisiblePosts(Builder $query, $user)
    {
        // Exclude reels, so we add 'where' condition to exclude 'reel' type
        return $this->scopeVisible($query, $user, 'post')
            ->whereNotIn('type', ['reel']); // Explicitly exclude 'reel' type
    }

    /**
     * Scope to filter reels based on visibility.
     */
    public function scopeVisibleReels(Builder $query, $user)
    {
        return $this->scopeVisible($query, $user, 'reel'); // Call the common function for 'reel'
    }
}
