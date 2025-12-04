<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\CreateHighlightRequest;
use App\Http\Resources\StoryHighlightResource;
use App\Models\User;
use App\Models\UserStory;
use App\Models\UserStoryHighlight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\AuthorizesRequests;

class StoryHighlightController extends Controller
{
    use AuthorizesRequests;
    /**
     * Get Highlights for a specific User (or logged in user).
     */
    public function index(Request $request, string $username = null): JsonResponse
    {
        try {
            // Determine target user
            if ($username) {
                $user = User::where('username', $username)->firstOrFail();
            } else {
                $user = Auth::user();
            }

            $highlights = UserStoryHighlight::with(['stories.media'])
                ->where('user_id', $user->id)
                ->latest()
                ->get(); // Highlights are usually limited, so paginate might not be needed strictly, but good to have if many

            return response()->json([
                'success' => true,
                'data' => StoryHighlightResource::collection($highlights)
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'User not found or error loading highlights.'], 404);
        }
    }

    /**
     * Show a single highlight with its stories.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $highlight = UserStoryHighlight::with(['stories.media', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => new StoryHighlightResource($highlight)]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Highlight not found.'], 404);
        }
    }

    /**
     * Create a Highlight.
     */
    public function store(CreateHighlightRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $highlight = UserStoryHighlight::create([
                'user_id' => $user->id,
                'title' => $request->title,
            ]);

            // Handle Cover Image
            if ($request->hasFile('cover_media')) {
                $media = $highlight->addMedia($request->file('cover_media'))
                    ->toMediaCollection('cover_media');

                $highlight->update(['cover_media_id' => $media->id]);
            }

            // Optionally add stories immediately during creation
            if ($request->filled('story_ids')) {
                // Ensure stories belong to user
                $storyIds = UserStory::whereIn('id', $request->story_ids)
                    ->where('user_id', $user->id)
                    ->pluck('id');

                $highlight->stories()->sync($storyIds);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Highlight created.',
                'data' => new StoryHighlightResource($highlight->load('stories'))
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Highlight Create Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create highlight.'], 500);
        }
    }

    /**
     * Add Story to Highlight.
     */
    public function addStory(int $highlightId, int $storyId): JsonResponse
    {
        try {
            $user = Auth::user();

            // Fetch Highlight
            $highlight = UserStoryHighlight::where('id', $highlightId)->where('user_id', $user->id)->firstOrFail();

            // Fetch Story (Ensure ownership)
            $story = UserStory::where('id', $storyId)->where('user_id', $user->id)->firstOrFail();

            // Sync without detaching (adds if not exists)
            $highlight->stories()->syncWithoutDetaching([$story->id]);

            return response()->json([
                'success' => true,
                'message' => 'Story added to highlight.',
                'data' => new StoryHighlightResource($highlight->fresh('stories.media'))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Could not add story.'], 400);
        }
    }

    /**
     * Remove Story from Highlight.
     */
    public function removeStory(int $highlightId, int $storyId): JsonResponse
    {
        try {
            $user = Auth::user();
            $highlight = UserStoryHighlight::where('id', $highlightId)->where('user_id', $user->id)->firstOrFail();

            $highlight->stories()->detach($storyId);

            return response()->json([
                'success' => true,
                'message' => 'Story removed from highlight.',
                'data' => new StoryHighlightResource($highlight->fresh('stories.media'))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Could not remove story.'], 400);
        }
    }
}
