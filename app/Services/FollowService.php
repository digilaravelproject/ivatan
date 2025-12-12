<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FollowService
{
    /**
     * Follow a user.
     * * @param User $follower The user performing the follow action (Auth user).
     * @param int|string $followingId The ID of the user to be followed.
     * @return array ['success' => bool, 'message' => string, 'code' => ?string]
     */
    public function followUser(User $follower, $followingId): array
    {
        // 1. Self-follow check
        if ((int)$follower->id === (int)$followingId) {
            return [
                'success' => false,
                'message' => 'You cannot follow yourself.',
                'code' => 'SELF_ACTION_FORBIDDEN'
            ];
        }

        DB::beginTransaction();

        try {
            // Retrieve user to follow with exclusive lock to prevent race conditions
            $userToFollow = User::lockForUpdate()->find($followingId);

            if (!$userToFollow) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User not found.', 'code' => 'NOT_FOUND'];
            }

            // Already following check
            if ($follower->isFollowing($userToFollow)) {
                DB::rollBack();
                return [
                    'success' => true,
                    'message' => 'Already following.',
                    'code' => 'ALREADY_FOLLOWING'
                ];
            }

            // Attach relation
            $follower->following()->attach($followingId);

            // Manual count updates for performance and atomicity
            $follower->increment('following_count');
            $userToFollow->increment('followers_count');

            DB::commit();

            return ['success' => true, 'message' => 'Followed successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Follow Service Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong.', 'code' => 'SERVER_ERROR'];
        }
    }

    /**
     * Unfollow a user.
     * * @param User $follower The user performing the unfollow action (Auth user).
     * @param int|string $followingId The ID of the user to be unfollowed.
     * @return array ['success' => bool, 'message' => string, 'code' => ?string]
     */
    public function unfollowUser(User $follower, $followingId): array
    {
        // 1. Self-unfollow check
        if ((int)$follower->id === (int)$followingId) {
            return [
                'success' => false,
                'message' => 'You cannot unfollow yourself.',
                'code' => 'SELF_ACTION_FORBIDDEN'
            ];
        }

        DB::beginTransaction();

        try {
            // Retrieve user to unfollow with exclusive lock
            $userToUnfollow = User::lockForUpdate()->find($followingId);

            if (!$userToUnfollow) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User not found.', 'code' => 'NOT_FOUND'];
            }

            // Check if user is currently following
            if (!$follower->isFollowing($userToUnfollow)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'You are not following this user.',
                    'code' => 'NOT_FOLLOWING'
                ];
            }

            // Detach relation
            $follower->following()->detach($followingId);

            // Decrement counts (with check to prevent count from going below zero)
            if ($follower->following_count > 0) {
                $follower->decrement('following_count');
            }
            if ($userToUnfollow->followers_count > 0) {
                $userToUnfollow->decrement('followers_count');
            }

            DB::commit();

            return ['success' => true, 'message' => 'Unfollowed successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Unfollow Service Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong.', 'code' => 'SERVER_ERROR'];
        }
    }
}
