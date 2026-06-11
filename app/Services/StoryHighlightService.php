<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStory;
use App\Models\UserStoryHighlight;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StoryHighlightService
{
    /**
     * Get Highlights for a specific User (or logged in user).
     */
    public function getUserHighlights(?string $username, User $authUser): Collection
    {
        if ($username) {
            $user = User::where('username', $username)->firstOrFail();
        } else {
            $user = $authUser;
        }

        return UserStoryHighlight::with(['stories.media'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    /**
     * Show a single highlight with its stories.
     */
    public function getHighlightById(int $id): UserStoryHighlight
    {
        return UserStoryHighlight::with(['stories.media', 'user'])->findOrFail($id);
    }

    /**
     * Create a Highlight.
     */
    public function createHighlight(User $user, string $title, $coverMedia = null, ?array $storyIds = null): UserStoryHighlight
    {
        return DB::transaction(function () use ($user, $title, $coverMedia, $storyIds) {
            $highlight = UserStoryHighlight::create([
                'user_id' => $user->id,
                'title' => $title,
            ]);

            // Handle Cover Image
            if ($coverMedia) {
                $media = $highlight->addMedia($coverMedia)
                    ->toMediaCollection('cover_media');

                $highlight->update(['cover_media_id' => $media->id]);
            }

            // Optionally add stories immediately during creation
            if (!empty($storyIds)) {
                // Ensure stories belong to user
                $validStoryIds = UserStory::whereIn('id', $storyIds)
                    ->where('user_id', $user->id)
                    ->pluck('id');

                $highlight->stories()->sync($validStoryIds);
            }

            return $highlight;
        });
    }

    /**
     * Add Story to Highlight.
     */
    public function addStoryToHighlight(User $user, int $highlightId, int $storyId): UserStoryHighlight
    {
        // Fetch Highlight
        $highlight = UserStoryHighlight::where('id', $highlightId)->where('user_id', $user->id)->firstOrFail();

        // Fetch Story (Ensure ownership)
        $story = UserStory::where('id', $storyId)->where('user_id', $user->id)->firstOrFail();

        // Sync without detaching (adds if not exists)
        $highlight->stories()->syncWithoutDetaching([$story->id]);

        return $highlight->fresh('stories.media');
    }

    /**
     * Remove Story from Highlight.
     */
    public function removeStoryFromHighlight(User $user, int $highlightId, int $storyId): UserStoryHighlight
    {
        $highlight = UserStoryHighlight::where('id', $highlightId)->where('user_id', $user->id)->firstOrFail();

        $highlight->stories()->detach($storyId);

        return $highlight->fresh('stories.media');
    }

    /**
     * Delete a Highlight and clean up all dependencies.
     */
    public function deleteHighlight(User $user, int $id): bool
    {
        return DB::transaction(function () use ($user, $id) {
            // Fetch highlight and ensure ownership
            $highlight = UserStoryHighlight::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Explicitly detach all stories associated with this highlight
            $highlight->stories()->detach();

            // Delete the highlight (Spatie MediaLibrary will auto-cleanup media)
            $highlight->delete();

            return true;
        });
    }
}
