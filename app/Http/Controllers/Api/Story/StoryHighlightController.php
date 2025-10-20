<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\AddStoryToHighlightRequest;
use App\Http\Requests\Story\CreateHighlightRequest;
use App\Http\Resources\StoryHighlightResource;
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


    public function addStory($id, AddStoryToHighlightRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            $highlight = UserStoryHighlight::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $story = UserStory::findOrFail($request->story_id);

            // Check ownership of the story
            if ($story->user_id !== $user->id) {
                return response()->json(['error' => 'You can only add your own stories.'], 403);
            }

            // Attach the story without detaching existing ones
            $highlight->stories()->syncWithoutDetaching([$story->id]);

            // Reload with media for accurate response
            $highlight->load('stories.media');

            return response()->json([
                'message' => 'Story successfully added to highlight.',
                'highlight' => new StoryHighlightResource($highlight)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Highlight or Story not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Error adding story to highlight: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while adding the story to the highlight.'
            ], 500);
        }
    }


    public function removeStory($id, AddStoryToHighlightRequest $request): JsonResponse
    {
        $user = auth()->user();
        $highlight = UserStoryHighlight::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $story = UserStory::findOrFail($request->story_id);

        $highlight->stories()->detach($story->id);

        return response()->json(['message' => 'Story removed from highlight', 'highlight' => new StoryHighlightResource($highlight->load('stories.media'))]);
    }

    public function show($id): JsonResponse
    {
        $highlight = UserStoryHighlight::with('stories.media')->findOrFail($id);
        return response()->json(new StoryHighlightResource($highlight));
    }
}
