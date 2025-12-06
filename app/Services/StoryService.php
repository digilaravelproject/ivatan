<?php

namespace App\Services;

use App\Jobs\GenerateThumbnailJob;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StoryService
{
    /**
     * Get Main Feed: Prioritizes Followed Users > Public Users.
     * Algorithm: "Followed Users First", then "Latest Story Update".
     */
    public function getFeed(User $user, int $perPage = 10): CursorPaginator
    {
        // 1. Get Following IDs + Self
        $followingIds = $user->following()->pluck('users.id')->toArray();
        $followingIds[] = $user->id;

        // Prepare string for SQL sorting
        $idsString = !empty($followingIds) ? implode(',', $followingIds) : '0';

        return User::query()
            ->whereHas('stories', fn($q) => $q->active())
            // Privacy Logic: Show if (I follow them) OR (They are Public)
            ->where(function ($q) use ($followingIds) {
                $q->whereIn('id', $followingIds)
                    ->orWhere('account_privacy', 'public');
            })
            // Eager Load Data
            ->with(['stories' => function ($q) use ($user) {
                $q->active()
                    ->orderBy('created_at', 'asc')
                    ->with(['media'])
                    ->withCount(['views', 'likes'])
                    // Optimize: Load boolean flags (0/1) instead of loading full objects
                    ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $user->id)])
                    ->withExists(['views as is_viewed' => fn($q) => $q->where('user_id', $user->id)]);
            }])
            // Get Latest Story Time for Sorting
            ->withMax(['stories as latest_story_at' => fn($q) => $q->active()], 'created_at')
            // --- SORTING ALGORITHM ---
            // 1. Followed users get priority (1), others (0)
            ->orderByRaw("CASE WHEN id IN ($idsString) THEN 1 ELSE 0 END DESC")
            // 2. Then sort by latest story time
            ->orderByDesc('latest_story_at')
            ->cursorPaginate($perPage);
    }

    /**
     * Get Stories for a specific user (Profile View) with Privacy Checks.
     */
    public function getUserStories(User $authUser, string $username): array
    {
        $targetUser = User::where('username', $username)->firstOrFail();

        // Privacy Check
        $canView = ($authUser->id === $targetUser->id) ||
            ($targetUser->account_privacy === 'public') ||
            ($authUser->isFollowing($targetUser));

        if (!$canView) {
            return ['status' => 'private', 'user' => $targetUser];
        }

        $stories = $targetUser->stories()
            ->active()
            ->orderBy('created_at', 'asc')
            ->with(['media'])
            ->withCount(['views', 'likes'])
            ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $authUser->id)])
            ->withExists(['views as is_viewed' => fn($q) => $q->where('user_id', $authUser->id)])
            ->get();

        return ['status' => 'success', 'user' => $targetUser, 'stories' => $stories];
    }

    /**
     * Get specific story details.
     */
    public function getStoryById(User $authUser, int $storyId): ?UserStory
    {
        $story = UserStory::with(['user', 'media'])
            ->withCount(['views', 'likes'])
            ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $authUser->id)])
            ->find($storyId);

        if (!$story) return null;

        // Privacy Check
        $owner = $story->user;
        $canView = ($authUser->id === $owner->id) ||
            ($owner->account_privacy === 'public') ||
            ($authUser->isFollowing($owner));

        return $canView ? $story : null;
    }

    /**
     * Create a new story.
     */
    public function createStory(User $user, array $data, UploadedFile $file): UserStory
    {
        return DB::transaction(function () use ($user, $data, $file) {
            $mimeType = $file->getMimeType();
            $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

            $story = UserStory::create([
                'user_id'    => $user->id,
                'caption'    => $data['caption'] ?? null,
                'meta'       => $data['meta'] ?? null,
                'type'       => $type,
                'expires_at' => isset($data['expires_at'])
                    ? now()->parse($data['expires_at'])
                    : now()->addHours(24),
            ]);

            $story->addMedia($file)->toMediaCollection('stories');

            // Dispatch Job only for VIDEO. Images are handled by Model Conversions automatically.
            if ($type === 'video') {
                dispatch(new GenerateThumbnailJob($story));
            }

            return $story;
        });
    }

    /**
     * Mark story as viewed (Idempotent).
     */
    public function markAsViewed(User $user, int $storyId): void
    {
        $story = UserStory::find($storyId);
        // Owner views don't count
        if ($story && $story->user_id !== $user->id) {
            $story->views()->firstOrCreate(
                ['user_id' => $user->id],
                ['ip_address' => request()->ip()]
            );
        }
    }

    /**
     * Toggle Like on a story.
     */
    public function toggleLike(User $user, int $storyId): array
    {
        return DB::transaction(function () use ($user, $storyId) {
            $story = UserStory::active()->lockForUpdate()->findOrFail($storyId);
            $result = $story->likes()->toggle($user->id);
            $isLiked = count($result['attached']) > 0;

            $isLiked ? $story->increment('like_count') : $story->decrement('like_count');

            return [
                'is_liked' => $isLiked,
                'count' => $story->refresh()->like_count
            ];
        });
    }

    public function deleteStory(User $user, int $storyId): bool
    {
        $story = UserStory::where('user_id', $user->id)->find($storyId);
        if ($story) {
            $story->delete();
            return true;
        }
        return false;
    }
}
