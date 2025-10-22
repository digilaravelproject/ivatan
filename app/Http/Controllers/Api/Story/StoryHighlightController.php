<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\AddStoryToHighlightRequest;
use App\Http\Requests\Story\CreateHighlightRequest;
use App\Http\Resources\StoryHighlightResource;
use App\Models\User;
use App\Models\UserStory;
use App\Models\UserStoryHighlight;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoryHighlightController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Optional: Cache key per user
        $cacheKey = "user:{$user->id}:highlights";

        // Optional: enable cache via query param
        $useCache = $request->boolean('cache', true);

        if ($useCache && Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Get only user's highlights and exclude expired stories
        $highlights = UserStoryHighlight::with(['stories' => function ($query) {
            $query->where('expires_at', '>', now())->with('media');
        }])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($request->get('per_page', 10));

        $response = [
            'data' => StoryHighlightResource::collection($highlights),
            'pagination' => [
                'current_page' => $highlights->currentPage(),
                'total_pages' => $highlights->lastPage(),
                'total_items' => $highlights->total(),
                'per_page' => $highlights->perPage(),
            ]
        ];

        if ($useCache) {
            Cache::put($cacheKey, $response, now()->addDay());
        }

        return response()->json($response);
    }


    public function getUserHighlights(Request $request, User $user = null): JsonResponse

    {
        try {
            $authUser = auth()->user();

            if (! $authUser) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // Use route param user if provided, else use auth user
            $targetUser = $user ?? $authUser;

            $cacheKey = "user:{$targetUser->id}:highlights";
            $useCache = $request->boolean('cache', true);

            if ($useCache && Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey));
            }

            $highlights = UserStoryHighlight::with(['stories' => function ($query) {
                $query->where('expires_at', '>', now())->with('media');
            }])
                ->where('user_id', $targetUser->id)
                ->latest()
                ->paginate($request->get('per_page', 10));

            $response = [
                'data' => StoryHighlightResource::collection($highlights),
                'pagination' => [
                    'current_page' => $highlights->currentPage(),
                    'total_pages' => $highlights->lastPage(),
                    'total_items' => $highlights->total(),
                    'per_page' => $highlights->perPage(),
                ]
            ];

            if ($useCache) {
                Cache::put($cacheKey, $response, now()->addDay());
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error fetching highlights: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while fetching highlights.'
            ], 500);
        }
    }


    public function store(CreateHighlightRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // Pehle highlight create kar le bina cover_media_id ke
            $highlight = UserStoryHighlight::create([
                'user_id' => $user->id,
                'title' => $request->title,
            ]);

            // Agar frontend se file upload hai to spatie media library me upload kar
            if ($request->hasFile('cover_media')) {
                // Add media to collection, spatie media library me automatic handling hogi
                $media = $highlight->addMedia($request->file('cover_media'))
                    ->usingFileName(uniqid() . '.' . $request->file('cover_media')->getClientOriginalExtension())
                    ->toMediaCollection('cover_media');

                // Compress and convert 1:1 aspect ratio - spatie media library me conversion use kar sakta hai
                // Assuming media conversions configured in UserStoryHighlight model

                // Update cover_media_id with saved media id
                $highlight->cover_media_id = $media->id;
                $highlight->save();
            }

            // Load related stories and media
            $highlight->load('stories.media');

            return response()->json(new StoryHighlightResource($highlight), 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create story highlight: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to create highlight. Please try again later.'
            ], 500);
        }
    }



    public function addStory($highlightId, $storyId): JsonResponse
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // Step 1: Find highlight belonging to the authenticated user
            $highlight = UserStoryHighlight::where('id', $highlightId)
                ->where('user_id', $user->id)
                ->first();

            if (! $highlight) {
                return response()->json(['error' => 'Highlight not found.'], 404);
            }

            // Step 2: Find the story by ID
            $story = UserStory::find($storyId);

            if (! $story) {
                return response()->json(['error' => 'Story not found.'], 404);
            }

            // Step 3: Verify the story belongs to the user
            if ($story->user_id !== $user->id) {
                return response()->json(['error' => 'You can only add your own stories.'], 403);
            }

            // Step 4: Check if story already attached
            if ($highlight->stories()->where('story_id', $story->id)->exists()) {
                return response()->json([
                    'message' => 'Story already added to highlight.',
                    'highlight' => new StoryHighlightResource($highlight->load('stories.media')),
                ], 200);
            }

            // Step 5: Attach the story without detaching existing ones
            $highlight->stories()->syncWithoutDetaching([$story->id]);

            // Step 6: Reload relations for accurate API response
            $highlight->load('stories.media');

            return response()->json([
                'message' => 'Story successfully added to highlight.',
                'highlight' => new StoryHighlightResource($highlight),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error adding story to highlight:', [
                'user_id' => auth()->id(),
                'highlight_id' => $highlightId,
                'story_id' => $storyId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred while adding the story to the highlight.',
            ], 500);
        }
    }




    public function removeStory($highlightId, $storyId): JsonResponse
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            $highlight = UserStoryHighlight::where('id', $highlightId)
                ->where('user_id', $user->id)
                ->first();

            if (! $highlight) {
                return response()->json(['error' => 'Highlight not found.'], 404);
            }

            $story = UserStory::find($storyId);

            if (! $story) {
                return response()->json(['error' => 'Story not found.'], 404);
            }

            if ($story->user_id !== $user->id) {
                return response()->json(['error' => 'You can only remove your own stories.'], 403);
            }

            // Detach the story from the highlight
            $highlight->stories()->detach($story->id);

            // Reload relationships to return updated data
            $highlight->load('stories.media');

            return response()->json([
                'message' => 'Story successfully removed from highlight.',
                'highlight' => new StoryHighlightResource($highlight),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error removing story from highlight:', [
                'user_id' => auth()->id(),
                'highlight_id' => $highlightId,
                'story_id' => $storyId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred while removing the story from the highlight.',
            ], 500);
        }
    }
    public function show($highlightId): JsonResponse
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // Fetch the highlight with stories and their media, ensuring it belongs to the user
            $highlight = UserStoryHighlight::with('stories.media')
                ->where('id', $highlightId)
                ->where('user_id', $user->id)
                ->first();

            if (! $highlight) {
                return response()->json(['error' => 'Highlight not found or access denied.'], 404);
            }

            return response()->json(new StoryHighlightResource($highlight));
        } catch (\Throwable $e) {
            \Log::error('Error fetching user story highlight:', [
                'user_id' => auth()->id(),
                'highlight_id' => $highlightId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred while fetching the highlight.',
            ], 500);
        }
    }
}
