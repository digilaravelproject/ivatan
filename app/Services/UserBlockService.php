<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserBlockService
{
    /**
     * Check if a bidirectional block relation exists between two users.
     * Checks are cached for performance.
     */
    public function hasBlockRelation(int $userId, int $targetId): bool
    {
        $cacheKey = "user_block_relation:{$userId}:{$targetId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userId, $targetId) {
            return UserBlock::where(function ($q) use ($userId, $targetId) {
                $q->where('user_id', $userId)->where('blocked_user_id', $targetId);
            })->orWhere(function ($q) use ($userId, $targetId) {
                $q->where('user_id', $targetId)->where('blocked_user_id', $userId);
            })->exists();
        });
    }

    /**
     * Toggle block/unblock on a user.
     */
    public function toggleBlock(User $blocker, int $targetUserId): array
    {
        if ($blocker->id === $targetUserId) {
            return [
                'success' => false,
                'message' => 'You cannot block yourself.',
                'is_blocked' => false,
                'code' => 'SELF_ACTION_FORBIDDEN',
            ];
        }

        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'is_blocked' => false,
                'code' => 'NOT_FOUND',
            ];
        }

        $existing = UserBlock::where('user_id', $blocker->id)
            ->where('blocked_user_id', $targetUser->id)
            ->first();

        if ($existing) {
            return $this->unblockUser($blocker, $targetUserId);
        }

        return $this->blockUser($blocker, $targetUserId);
    }

    /**
     * Block a user.
     */
    public function blockUser(User $blocker, int $targetUserId): array
    {
        if ($blocker->id === $targetUserId) {
            return [
                'success' => false,
                'message' => 'You cannot block yourself.',
                'is_blocked' => false,
                'code' => 'SELF_ACTION_FORBIDDEN',
            ];
        }

        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'is_blocked' => false,
                'code' => 'NOT_FOUND',
            ];
        }

        try {
            return DB::transaction(function () use ($blocker, $targetUser) {
                // Create block record
                UserBlock::firstOrCreate([
                    'user_id' => $blocker->id,
                    'blocked_user_id' => $targetUser->id,
                ]);

                // Bidirectional follower cleanup
                DB::table('followers')
                    ->where(function ($q) use ($blocker, $targetUser) {
                        $q->where('follower_id', $blocker->id)
                            ->where('following_id', $targetUser->id);
                    })
                    ->orWhere(function ($q) use ($blocker, $targetUser) {
                        $q->where('follower_id', $targetUser->id)
                            ->where('following_id', $blocker->id);
                    })
                    ->delete();

                // Recalculate follow counts
                $this->recalculateFollowCounts($blocker);
                $this->recalculateFollowCounts($targetUser);

                // Clear caches
                $this->clearBlockCache($blocker->id, $targetUser->id);

                return [
                    'success' => true,
                    'message' => 'User blocked successfully.',
                    'is_blocked' => true,
                    'code' => 'BLOCKED',
                ];
            });
        } catch (\Exception $e) {
            Log::error("Block User Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to process block action.',
                'is_blocked' => false,
                'code' => 'SERVER_ERROR',
            ];
        }
    }

    /**
     * Unblock a user.
     */
    public function unblockUser(User $blocker, int $targetUserId): array
    {
        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'is_blocked' => false,
                'code' => 'NOT_FOUND',
            ];
        }

        try {
            $existing = UserBlock::where('user_id', $blocker->id)
                ->where('blocked_user_id', $targetUserId)
                ->first();

            if ($existing) {
                $existing->delete();
            }

            // Clear caches
            $this->clearBlockCache($blocker->id, $targetUserId);

            return [
                'success' => true,
                'message' => 'User unblocked successfully.',
                'is_blocked' => false,
                'code' => 'UNBLOCKED',
            ];
        } catch (\Exception $e) {
            Log::error("Unblock User Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to process unblock action.',
                'is_blocked' => false,
                'code' => 'SERVER_ERROR',
            ];
        }
    }

    /**
     * Clear block relationship cache.
     */
    public function clearBlockCache(int $userId, int $targetId): void
    {
        Cache::forget("user_block_relation:{$userId}:{$targetId}");
        Cache::forget("user_block_relation:{$targetId}:{$userId}");
        
        // Clear instance cache on active user objects if loaded
        if ($user = User::find($userId)) {
            $user->clearBlockCache();
        }
        if ($target = User::find($targetId)) {
            $target->clearBlockCache();
        }
    }

    /**
     * Recalculate follower/following counts.
     */
    protected function recalculateFollowCounts(User $user): void
    {
        $followersCount = DB::table('followers')->where('following_id', $user->id)->count();
        $followingCount = DB::table('followers')->where('follower_id', $user->id)->count();

        $user->update([
            'followers_count' => $followersCount,
            'following_count' => $followingCount,
        ]);
    }

    /**
     * Get users blocked by the given user (for "blocked users" list).
     * Optimized with eager loading.
     */
    public function getBlockedUsers(User $user, int $perPage = 20)
    {
        return UserBlock::where('user_id', $user->id)
            ->with(['blockedUser' => function ($q) {
                $q->select(['id', 'name', 'username', 'profile_photo_path', 'is_verified'])
                    ->with('media'); // For profile_photo_url accessor
            }])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }
}
