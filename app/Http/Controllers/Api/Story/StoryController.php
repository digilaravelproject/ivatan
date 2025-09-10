<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Models\StoryLike;
use App\Models\UserStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum')->only(['store','like','unlike','myStories']);
    // }

    // get active stories (all users) — you can refine to only following users
    public function index(): JsonResponse
    {
        $stories = UserStory::with('user','media')
            ->where('expires_at', '>', now())
            ->orderBy('created_at','desc')
            ->get();

        return response()->json(StoryResource::collection($stories));
    }

    // show one story
    public function show(UserStory $story): JsonResponse
    {
        if ($story->expires_at <= now()) {
            return response()->json(['message'=>'Story expired'], 404);
        }

        return response()->json(new StoryResource($story->load('user','media')));
    }

    // create story
    public function store(StoreStoryRequest $request): JsonResponse
    {
        $user = auth()->user();

        $expiresAt = $request->input('expires_at') ? now()->parse($request->input('expires_at')) : now()->addHours(24);

        $story = DB::transaction(function () use ($request, $user, $expiresAt) {
            $story = UserStory::create([
                'user_id' => $user->id,
                'caption' => $request->caption,
                'meta' => $request->meta,
                'type' => str_starts_with($request->file('media')->getMimeType(), 'video/') ? 'video' : 'image',
                'expires_at' => $expiresAt,
            ]);

            // attach media (single file)
            $media = $story->addMedia($request->file('media'))
                ->toMediaCollection('stories');

            // store text overlay as custom property on media (so client can render text exactly)
            if ($request->filled('caption') || $request->filled('meta')) {
                $media->setCustomProperty('caption', $request->caption);
                $media->setCustomProperty('meta', $request->meta ?? []);
                $media->save();
            }

            return $story;
        });

        return response()->json(new StoryResource($story->load('media','user')), 201);
    }

    // like story
    public function like(UserStory $story): JsonResponse
    {
        $user = auth()->user();

        if ($story->expires_at <= now()) {
            return response()->json(['message' => 'Story expired'], 400);
        }

        $exists = $story->likes()->where('user_id', $user->id)->exists();
        if ($exists) {
            return response()->json(['message' => 'Already liked'], 400);
        }

        $story->likes()->create(['user_id' => $user->id]);
        $story->increment('like_count');

        // TODO: send notification to story owner (optional)

        return response()->json(['message' => 'Liked', 'like_count' => $story->like_count + 1]);
    }

    // unlike / remove like
    public function unlike(UserStory $story): JsonResponse
    {
        $user = auth()->user();

        $like = $story->likes()->where('user_id', $user->id)->first();
        if (! $like) {
            return response()->json(['message' => 'Not liked yet'], 400);
        }

        $like->delete();
        $story->decrement('like_count');

        return response()->json(['message' => 'Unliked', 'like_count' => max(0, $story->like_count - 1)]);
    }

    // stories of authenticated user (active + archived optionally)
    public function myStories(Request $request): JsonResponse
    {
        $user = auth()->user();

        $showExpired = $request->boolean('expired', false);

        $query = Story::with('media')
            ->where('user_id', $user->id);

        if (! $showExpired) {
            $query->where('expires_at', '>', now());
        }

        $stories = $query->orderBy('created_at','desc')->get();

        return response()->json(StoryResource::collection($stories));
    }
}
