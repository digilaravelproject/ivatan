<?php

namespace App\Services;

use App\Jobs\GenerateThumbnailJob;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StoryService
{
    /**
     * Fetches the main story feed.
     * Logic: Shows stories from people you follow first, then other public stories.
     * Uses a custom sorting algorithm to prevent stories from jumping around (jitter).
     */
    public function getFeed(User $user, int $perPage = 10): Paginator
    {
        // specific IDs to prioritize (Current user + Following)
        $followingIds = $user->following()->pluck('users.id')->toArray();
        $followingIds[] = $user->id;

        // Convert array to string for the raw SQL "ORDER BY" clause later
        $idsString = !empty($followingIds) ? implode(',', $followingIds) : '0';

        return User::query()
            ->whereHas('stories', fn($q) => $q->active())
            // Privacy Check: Only show if I follow them OR if their account is public
            ->where(function ($q) use ($followingIds) {
                $q->whereIn('id', $followingIds)
                    ->orWhere('account_privacy', 'public');
            })
            ->with(['stories' => function ($q) use ($user) {
                $q->active()
                    ->orderBy('created_at', 'asc')
                    ->with(['media'])
                    // Load counts (including comments) so the UI doesn't show "0"
                    ->withCount(['views', 'likes'])
                    // Preview: Load only the latest 5 comments for the feed to keep it fast
                    // ->with(['comments' => function($c) {
                    //     $c->latest()->limit(5)->with('user');
                    // }])
                    // Check if the current user has already liked/viewed these stories
                    ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $user->id)])
                    ->withExists(['views as is_viewed' => fn($q) => $q->where('user_id', $user->id)]);
            }])
            // We need the latest story timestamp to sort users who posted recently
            ->withMax(['stories as latest_story_at' => fn($q) => $q->active()], 'created_at')
            
            // --- Custom Sorting Logic ---
            // 1. Priority: Users I follow appear at the top.
            ->orderByRaw("CASE WHEN id IN ($idsString) THEN 1 ELSE 0 END DESC")
            // 2. Recency: Within those groups, sort by who posted most recently.
            ->orderByDesc('latest_story_at')
            ->simplePaginate($perPage);
    }

    /**
     * Fetches stories for a specific user profile (e.g., when tapping a profile picture).
     * Includes a privacy check to ensure the viewer has permission.
     */
    public function getUserStories(User $authUser, string $username): array
    {
        $targetUser = User::where('username', $username)->firstOrFail();

        // Check if the viewer is allowed to see these stories
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
            // For profile view, we load more comments (20) than the feed
            // ->with(['comments' => fn($q) => $q->latest()->limit(20)->with('user')])
            ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $authUser->id)])
            ->withExists(['views as is_viewed' => fn($q) => $q->where('user_id', $authUser->id)])
            ->get();

        return ['status' => 'success', 'user' => $targetUser, 'stories' => $stories];
    }

    /**
     * Fetches a single story by ID (e.g., from a notification).
     */
    public function getStoryById(User $authUser, int $storyId): ?UserStory
    {
        $story = UserStory::with(['user', 'media'])
            ->withCount(['views', 'likes'])
            // ->with(['comments' => fn($q) => $q->latest()->with('user')])
            ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $authUser->id)])
            ->find($storyId);

        if (!$story) return null;

        // Privacy check before returning
        $owner = $story->user;
        $canView = ($authUser->id === $owner->id) ||
            ($owner->account_privacy === 'public') ||
            ($authUser->isFollowing($owner));

        return $canView ? $story : null;
    }

    /**
     * Handles story creation.
     * Wraps DB insertion and file upload in a transaction for safety.
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

            // If it's a video, generate a thumbnail in the background to speed up the response
            if ($type === 'video') {
                dispatch(new GenerateThumbnailJob($story));
            }

            return $story;
        });
    }

    /**
     * Records a unique view for a story (IP-based protection included).
     */
    public function markAsViewed(User $user, int $storyId): void
    {
        $story = UserStory::find($storyId);
        // Prevent counting the user's own views on their story
        if ($story && $story->user_id !== $user->id) {
            $story->views()->firstOrCreate(
                ['user_id' => $user->id],
                ['ip_address' => request()->ip()]
            );
        }
    }

    /**
     * Toggles the like status.
     * CRITICAL: We manually update the 'like_count' and force the 'expires_at' date.
     * This prevents the default 'updated_at' behavior from accidentally extending 
     * or resetting the story's 24-hour expiration timer.
     */
    public function toggleLike(User $user, int $storyId): array
    {
        return DB::transaction(function () use ($user, $storyId) {
            $story = UserStory::findOrFail($storyId);
            
            // Toggle the like relationship in the pivot table
            $result = $story->likes()->toggle($user->id);
            $isLiked = count($result['attached']) > 0;

            // Manually update the count and preserve the original expiry time
            if ($isLiked) {
                DB::table('user_stories')
                    ->where('id', $storyId)
                    ->update([
                        'like_count' => DB::raw('like_count + 1'),
                        'expires_at' => $story->expires_at->toDateTimeString() 
                    ]);
            } else {
                DB::table('user_stories')
                    ->where('id', $storyId)
                    ->where('like_count', '>', 0)
                    ->update([
                        'like_count' => DB::raw('like_count - 1'),
                        'expires_at' => $story->expires_at->toDateTimeString()
                    ]);
            }

            // Return the fresh count directly from DB
            $newCount = DB::table('user_stories')
                ->where('id', $storyId)
                ->value('like_count');

            return [
                'is_liked' => $isLiked,
                'count' => $newCount
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