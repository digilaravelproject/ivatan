<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\CreateHighlightRequest;
use App\Http\Resources\StoryHighlightResource;
use App\Services\StoryHighlightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoryHighlightController extends Controller
{
    protected StoryHighlightService $highlightService;

    public function __construct(StoryHighlightService $highlightService)
    {
        $this->highlightService = $highlightService;
    }

    /**
     * Get Highlights for a specific User (or logged in user).
     */
    public function index(Request $request, string $username = null): JsonResponse
    {
        try {
            $user = Auth::user();
            $highlights = $this->highlightService->getUserHighlights($username, $user);

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
            $highlight = $this->highlightService->getHighlightById($id);
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
        try {
            $user = Auth::user();
            $highlight = $this->highlightService->createHighlight(
                $user,
                $request->title,
                $request->file('cover_media'),
                $request->story_ids
            );

            return response()->json([
                'success' => true,
                'message' => 'Highlight created.',
                'data' => new StoryHighlightResource($highlight->load('stories'))
            ], 201);
        } catch (\Throwable $e) {
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
            $highlight = $this->highlightService->addStoryToHighlight($user, $highlightId, $storyId);

            return response()->json([
                'success' => true,
                'message' => 'Story added to highlight.',
                'data' => new StoryHighlightResource($highlight)
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
            $highlight = $this->highlightService->removeStoryFromHighlight($user, $highlightId, $storyId);

            return response()->json([
                'success' => true,
                'message' => 'Story removed from highlight.',
                'data' => new StoryHighlightResource($highlight)
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Could not remove story.'], 400);
        }
    }

    /**
     * Delete a Highlight and clean up all dependencies.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->highlightService->deleteHighlight($user, $id);

            return response()->json([
                'success' => true,
                'message' => 'Highlight deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Highlight not found or unauthorized.'
            ], 404);
        } catch (\Throwable $e) {
            Log::error("Highlight Delete Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete highlight due to a server error.'
            ], 500);
        }
    }
}
