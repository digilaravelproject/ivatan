<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FollowService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function followUser(User $follower, $followingId): array
    {
        if ((int)$follower->id === (int)$followingId) {
            return [
                'success' => false,
                'message' => 'You cannot follow yourself.',
                'code' => 'SELF_ACTION_FORBIDDEN'
            ];
        }

        DB::beginTransaction();

        try {
            $userToFollow = User::lockForUpdate()->find($followingId);

            if (!$userToFollow) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User not found.', 'code' => 'NOT_FOUND'];
            }

            if ($follower->hasBlockRelationWith($userToFollow)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Action forbidden due to block status.',
                    'code' => 'FORBIDDEN'
                ];
            }

            if ($follower->isFollowing($userToFollow)) {
                DB::rollBack();
                return [
                    'success' => true,
                    'message' => 'Already following.',
                    'code' => 'ALREADY_FOLLOWING'
                ];
            }

            $follower->following()->attach($followingId);

            $follower->increment('following_count');
            $userToFollow->increment('followers_count');

            DB::commit();

            // Send notification after commit
            try {
                $this->notificationService->sendToUser($userToFollow, 'follow', [
                    'title'        => 'New Follower',
                    'message'      => $follower->name . ' started following you',
                    'actor_id'     => $follower->id,
                    'actor_name'   => $follower->name,
                    'actor_avatar' => $follower->profile_photo_url,
                    'action_url'   => null,
                ]);
            } catch (\Throwable $e) {
                Log::error('Follow notification failed', ['error' => $e->getMessage()]);
            }

            return ['success' => true, 'message' => 'Followed successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Follow Service Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong.', 'code' => 'SERVER_ERROR'];
        }
    }

    public function unfollowUser(User $follower, $followingId): array
    {
        if ((int)$follower->id === (int)$followingId) {
            return [
                'success' => false,
                'message' => 'You cannot unfollow yourself.',
                'code' => 'SELF_ACTION_FORBIDDEN'
            ];
        }

        DB::beginTransaction();

        try {
            $userToUnfollow = User::lockForUpdate()->find($followingId);

            if (!$userToUnfollow) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User not found.', 'code' => 'NOT_FOUND'];
            }

            if (!$follower->isFollowing($userToUnfollow)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'You are not following this user.',
                    'code' => 'NOT_FOLLOWING'
                ];
            }

            $follower->following()->detach($followingId);

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
