<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Resources\StoryResource;
use App\Jobs\GenerateThumbnailJob;
use App\Models\Story;
use App\Models\UserStoryLike;
use App\Models\UserStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use FFMpeg;

class StoryController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum')->only(['store','like','unlike','myStories']);
    // }

    // get active stories (all users) — you can refine to only following users
    public function index(Request $request): JsonResponse
    {
        try {
            // Default to 10 stories per page, or get it from the query parameter
            $perPage = $request->get('per_page', 10);

            // Query to get active stories
            $stories = UserStory::with('user', 'media')
                ->where('expires_at', '>', now())  // Only active stories
                ->orderBy('created_at', 'desc')  // Sort by creation date (descending)
                ->paginate($perPage);  // Paginate the results

            // Return paginated stories along with pagination metadata
            return response()->json([
                'data' => StoryResource::collection($stories),
                'pagination' => [
                    'current_page' => $stories->currentPage(),
                    'total_pages' => $stories->lastPage(),
                    'total_items' => $stories->total(),
                    'per_page' => $stories->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error fetching stories: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching stories. Please try again.'], 500);
        }
    }


    // show one story
    public function show(UserStory $story): JsonResponse
    {
        try {
            // Check if the story has expired
            if ($story->expires_at <= now()) {
                return response()->json(['message' => 'Story expired'], 404);
            }

            // Return the story with associated user and media
            return response()->json(new StoryResource($story->load('user', 'media')));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Catch if the story does not exist (ModelNotFoundException will be thrown if the route model binding fails)
            return response()->json(['message' => 'Story not found'], 404);
        } catch (\Exception $e) {
            // Catch any other general exceptions and log the error
            \Log::error("Error fetching the story: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the story. Please try again.'], 500);
        }
    }


    /**
     * Store a new story.
     */
    public function store(StoreStoryRequest $request): JsonResponse
    {
        $user = auth()->user();
        $expiresAt = $request->input('expires_at') ? now()->parse($request->input('expires_at')) : now()->addHours(24);

        try {
            $story = DB::transaction(function () use ($request, $user, $expiresAt) {
                // Create the story
                $story = UserStory::create([
                    'user_id' => $user->id,
                    'caption' => $request->caption,
                    'meta' => $request->meta,
                    'type' => str_starts_with($request->file('media')->getMimeType(), 'video/') ? 'video' : 'image',
                    'expires_at' => $expiresAt,
                ]);

                // Attach media (the uploaded file)
                $media = $story->addMedia($request->file('media'))
                    ->toMediaCollection('stories');

                // Handle video thumbnail generation if the media is a video
                if ($story->type === 'video') {
                    // Dispatch job to generate video thumbnail, passing the Story model
                    dispatch(new GenerateThumbnailJob($story));  // Pass the Story model, not the file
                }

                // Store caption and meta as custom properties
                if ($request->filled('caption') || $request->filled('meta')) {
                    $media->setCustomProperty('caption', $request->caption);
                    $media->setCustomProperty('meta', $request->meta ?? []);
                    $media->save();
                }

                return $story;
            });

            return response()->json(new StoryResource($story->load('media', 'user')), 201);
        } catch (\Exception $e) {
            \Log::error("Error storing story: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving your story. Please try again.'], 500);
        }
    }



    // like story
    public function like(UserStory $story): JsonResponse
    {
        $user = auth()->user();

        if ($story->expires_at <= now()) {
            return response()->json(['message' => 'Story expired'], 400);
        }

        // Check if the user has already liked the story
        $exists = $story->likes()->where('user_id', $user->id)->exists();

        if ($exists) {
            // If the user has already liked the story, unlike it
            $story->likes()->detach($user->id);  // Remove the like
            $story->decrement('like_count');     // Decrease the like count

            return response()->json(['message' => 'Unliked', 'like_count' => max(0, $story->like_count - 1)]);
        } else {
            // If the user hasn't liked the story yet, like it
            $story->likes()->attach($user->id);  // Add the like
            $story->increment('like_count');     // Increase the like count

            return response()->json(['message' => 'Liked', 'like_count' => $story->like_count + 1]);
        }
    }

    // Unlike / remove like
    public function unlike(UserStory $story): JsonResponse
    {
        $user = auth()->user();

        try {
            // Check if the user has liked the story
            $like = $story->likes()->where('user_id', $user->id)->first();

            if (!$like) {
                // If the user hasn't liked the story yet, return a message
                return response()->json(['message' => 'You have not liked this story yet.'], 400);
            }

            // Proceed to remove the like from the pivot table
            $story->likes()->detach($user->id);

            // Optionally, decrement like count
            $story->decrement('like_count');

            return response()->json(['message' => 'Unliked', 'like_count' => max(0, $story->like_count - 1)]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error processing unlike action: " . $e->getMessage());
            // Return a general error message
            return response()->json(['error' => 'An error occurred while processing your request. Please try again.'], 500);
        }
    }


    // stories of authenticated user (active + archived optionally)
    public function myStories(Request $request): JsonResponse
    {
        $user = auth()->user();

        try {
            // Check if we should show expired stories
            $showExpired = $request->boolean('expired', false);

            // Default to 10 stories per page (can be adjusted via the 'per_page' parameter)
            $perPage = $request->get('per_page', 10);

            // Start the query to get stories
            $query = Story::with('media')
                ->where('user_id', $user->id);

            // If not showing expired stories, filter them out
            if (! $showExpired) {
                $query->where('expires_at', '>', now());
            }

            // Paginate the results
            $stories = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Return paginated stories with metadata
            return response()->json([
                'data' => StoryResource::collection($stories),
                'pagination' => [
                    'current_page' => $stories->currentPage(),
                    'total_pages' => $stories->lastPage(),
                    'total_items' => $stories->total(),
                    'per_page' => $stories->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            // Log the error and return a message
            \Log::error("Error fetching user's stories: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching stories. Please try again.'], 500);
        }
    }
}
