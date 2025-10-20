<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Resources\StoryResource;
use App\Jobs\GenerateThumbnailJob;
use App\Models\User;
use App\Models\UserStoryLike;
use App\Models\UserStory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use FFMpeg;
use Illuminate\Support\Facades\Cache;

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


    public function show($id): JsonResponse
    {
        try {
            $story = UserStory::with(['user', 'media'])->findOrFail($id);

            // Cache logic
            $cacheKey = "story_{$story->id}";

            $cachedStory = Cache::remember($cacheKey, 60, function () use ($story) {
                if ($story->expires_at->lte(now())) {
                    return null;
                }
                return new StoryResource($story);
            });

            if (!$cachedStory) {
                return response()->json(['message' => 'Story expired'], 404);
            }

            return response()->json($cachedStory);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Story not found'], 404);
        } catch (\Exception $e) {
            \Log::error("Error fetching the story ID {$id}: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the story. Please try again.'], 500);
        }
    }


    // get stories of a specific user
    public function getUserStories(Request $request, $username): JsonResponse
    {
        try {
            $user = User::where('username', $username)->first();
            if (!$user) {
                return response()->json(['error' => "User {$username} not found."], 404);
            }

            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:100',
            ]);
            $perPage = $validated['per_page'] ?? 10;

            $cacheKey = "user_{$user->id}_stories_page_" . ($request->get('page', 1)) . "_perPage_{$perPage}";

            // Cache for 5 minutes (adjust as needed)
            $stories = Cache::remember($cacheKey, 300, function () use ($user, $perPage) {
                return UserStory::with('media')
                    ->where('user_id', $user->id)
                    ->where('expires_at', '>', now())
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
            });

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
            \Log::error("Error fetching stories for user {$username}: " . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while fetching stories. Please try again.'
            ], 500);
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
            Cache::forget("user:{$user->id}:stories");
            return response()->json(new StoryResource($story->load('media', 'user')), 201);
        } catch (\Exception $e) {
            \Log::error("Error storing story: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving your story. Please try again.'], 500);
        }
    }



    // like story
    public function like($story): JsonResponse
    {
        try {
            $user = auth()->user();
            $story = UserStory::findOrFail($story);
            if ($story->expires_at <= now()) {
                return response()->json(['message' => 'Story expired'], 400);
            }

            $exists = $story->likes()->where('user_id', $user->id)->exists();

            if ($exists) {
                // Unlike
                $story->likes()->detach($user->id);
                $story->decrement('like_count');
                $story->refresh();

                return response()->json(['message' => 'Unliked', 'like_count' => max(0, $story->like_count)]);
            } else {
                // Like
                $story->likes()->attach($user->id);
                $story->increment('like_count');
                $story->refresh();

                return response()->json(['message' => 'Liked', 'like_count' => $story->like_count]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Story not found.'], 404);
        } catch (\Exception $e) {
            \Log::error("Error liking/unliking story ID {$story->id}: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request. Please try again.'], 500);
        }
    }


    // Unlike / remove like

    public function unlike($story): JsonResponse
    {
        try {
            $user = auth()->user();
            $story = UserStory::findOrFail($story);

            $like = $story->likes()->where('user_id', $user->id)->first();

            if (!$like) {
                return response()->json(['message' => 'You have not liked this story yet.'], 400);
            }

            $story->likes()->detach($user->id);
            $story->decrement('like_count');
            $story->refresh();

            return response()->json(['message' => 'Unliked', 'like_count' => max(0, $story->like_count)]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Story not found.'], 404);
        } catch (\Exception $e) {
            \Log::error("Error processing unlike action: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request. Please try again.'], 500);
        }
    }



    // stories of authenticated user (active + archived optionally)
    public function myStories(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            $perPage = $request->get('per_page', 10);
            $cacheKey = "user:{$user->id}:stories";

            // Try to get cached stories
            $cached = Cache::get($cacheKey);

            if ($cached) {
                return response()->json($cached);
            }

            // Active (live) stories
            $liveStories = UserStory::with('media')
                ->where('user_id', $user->id)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($story) {
                    $story->tag = 'live';
                    return new StoryResource($story);
                });

            // Expired (archived) stories
            $archivedStories = UserStory::with('media')
                ->where('user_id', $user->id)
                ->where('expires_at', '<=', now())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($story) {
                    $story->tag = 'archived';
                    return new StoryResource($story);
                });

            // Response structure
            $response = [
                'live' => $liveStories,
                'archived' => $archivedStories,
                'pagination' => [
                    'live_count' => $liveStories->count(),
                    'archived_count' => $archivedStories->count(),
                    'total' => $liveStories->count() + $archivedStories->count(),
                ]
            ];

            // Cache for 1 day
            Cache::put($cacheKey, $response, now()->addDay());


            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error("Error fetching user's stories: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching stories. Please try again.'], 500);
        }
    }
}
