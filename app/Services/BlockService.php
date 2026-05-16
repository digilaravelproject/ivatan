<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockService
{
    /**
     * Toggle block/unblock on a user.
     *
     * @return array{success: bool, message: string, is_blocked: bool, code?: string}
     */
    public function toggleBlock(User $blocker, int $targetUserId): array
    {
        try {
            // Self-block prevention
            if ($blocker->id === $targetUserId) {
                return [
                    'success' => false,
                    'message' => 'You cannot block yourself.',
                    'is_blocked' => false,
                    'code' => 'SELF_ACTION_FORBIDDEN',
                ];
            }

            // Verify target user exists
            /** @var User|null $targetUser */
            $targetUser = User::find($targetUserId);
            if (!$targetUser) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                    'is_blocked' => false,
                    'code' => 'NOT_FOUND',
                ];
            }

            return DB::transaction(function () use ($blocker, $targetUser) {
                $existing = UserBlock::where('user_id', $blocker->id)
                    ->where('blocked_user_id', $targetUser->id)
                    ->first();

                if ($existing) {
                    // === UNBLOCK ===
                    $existing->delete();

                    // Clear per-request cache
                    $blocker->clearBlockCache();

                    return [
                        'success' => true,
                        'message' => 'User unblocked successfully. You can now see their content and interact.',
                        'is_blocked' => false,
                        'code' => 'UNBLOCKED',
                    ];
                }

                // === BLOCK ===
                UserBlock::create([
                    'user_id' => $blocker->id,
                    'blocked_user_id' => $targetUser->id,
                ]);

                // Also unfollow each other (bidirectional cleanup)
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

                // Decrement follower/following counts atomically
                $this->recalculateFollowCounts($blocker);
                $this->recalculateFollowCounts($targetUser);

                // Clear per-request cache
                $blocker->clearBlockCache();

                return [
                    'success' => true,
                    'message' => 'User blocked successfully. They will no longer see your content or interact with you.',
                    'is_blocked' => true,
                    'code' => 'BLOCKED',
                ];
            });
        } catch (\Exception $e) {
            Log::error("Block Toggle Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to process block action.',
                'is_blocked' => false,
                'code' => 'SERVER_ERROR',
            ];
        }
    }

    /**
     * Check if a block relationship exists (bidirectional).
     */
    public function isBlocked(int $userId, int $targetId): bool
    {
        return UserBlock::where(function ($q) use ($userId, $targetId) {
            $q->where('user_id', $userId)->where('blocked_user_id', $targetId);
        })->orWhere(function ($q) use ($userId, $targetId) {
            $q->where('user_id', $targetId)->where('blocked_user_id', $userId);
        })->exists();
    }

    /**
     * Get all blocked user IDs for feed filtering (single query).
     * Returns IDs of users blocked BY me + users who blocked ME.
     */
    public function getAllBlockedIds(User $user): array
    {
        return $user->getAllBlockedIds();
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

    /**
     * Recalculate follower/following counts from the source of truth.
     * Prevents count drift after block removes follow records.
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
}
