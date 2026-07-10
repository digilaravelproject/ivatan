<?php

namespace App\Traits;

use App\Models\User;
use App\Models\UserBlock;

trait BlocksUsers
{
    protected ?array $cachedBlockedIds = null;
    protected ?array $cachedBlockedByIds = null;

    /**
     * Users that I have blocked.
     */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_user_id')
            ->withTimestamps();
    }

    /**
     * Users that have blocked me.
     */
    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_user_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get IDs of users I have blocked (cached per request lifecycle).
     */
    public function getBlockedUserIds(): array
    {
        if ($this->cachedBlockedIds === null) {
            $this->cachedBlockedIds = UserBlock::where('user_id', $this->id)
                ->pluck('blocked_user_id')
                ->all();
        }
        return $this->cachedBlockedIds;
    }

    /**
     * Get IDs of users who have blocked me (cached per request lifecycle).
     */
    public function getBlockedByUserIds(): array
    {
        if ($this->cachedBlockedByIds === null) {
            $this->cachedBlockedByIds = UserBlock::where('blocked_user_id', $this->id)
                ->pluck('user_id')
                ->all();
        }
        return $this->cachedBlockedByIds;
    }

    /**
     * Get ALL user IDs to exclude from feed (users I blocked + users who blocked me).
     */
    public function getAllBlockedIds(): array
    {
        return array_unique(array_merge(
            $this->getBlockedUserIds(),
            $this->getBlockedByUserIds()
        ));
    }

    /**
     * Check if I have blocked a specific user.
     */
    public function hasBlocked(User $user): bool
    {
        return in_array($user->id, $this->getBlockedUserIds());
    }

    /**
     * Check if a specific user has blocked me.
     */
    public function isBlockedBy(User $user): bool
    {
        return in_array($user->id, $this->getBlockedByUserIds());
    }

    /**
     * Check if ANY block exists between me and another user (bidirectional).
     */
    public function hasBlockRelationWith(User $user): bool
    {
        return $this->hasBlocked($user) || $this->isBlockedBy($user);
    }

    /**
     * Clear the per-request block cache.
     */
    public function clearBlockCache(): void
    {
        $this->cachedBlockedIds = null;
        $this->cachedBlockedByIds = null;
    }
}
