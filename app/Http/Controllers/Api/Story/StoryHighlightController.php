<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddStoryToHighlightRequest;
use App\Http\Requests\CreateHighlightRequest;
use App\Http\Resources\StoryHighlightResource;
use App\Models\UserStory;
use App\Models\UserStoryHighlight;
use Illuminate\Http\JsonResponse;

class StoryHighlightController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        $highlights = UserStoryHighlight::with('stories.media')->latest()->get();
        return response()->json(StoryHighlightResource::collection($highlights));
    }

    public function store(CreateHighlightRequest $request): JsonResponse
    {
        $user = auth()->user();

        $highlight = UserStoryHighlight::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'cover_media_id' => $request->cover_media_id,
        ]);

        return response()->json(new StoryHighlightResource($highlight->load('stories.media')), 201);
    }

    public function addStory($id, AddStoryToHighlightRequest $request): JsonResponse
    {
        $user = auth()->user();
        $highlight = UserStoryHighlight::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $story = userStory::findOrFail($request->story_id);

        // only user can add their own story
        if ($story->user_id !== $user->id) {
            return response()->json(['message' => 'You can only add your own stories'], 403);
        }

        $highlight->stories()->syncWithoutDetaching([$story->id]);

        return response()->json(['message' => 'Story added to highlight', 'highlight' => new StoryHighlightResource($highlight->load('stories.media'))]);
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
