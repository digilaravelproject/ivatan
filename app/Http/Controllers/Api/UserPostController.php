<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\UserPost;
use App\Services\LikeService;
use App\Services\ReportService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessMediaJob;
use App\Models\User;
use App\Services\ViewTrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Validation\ValidationException;


class UserPostController extends Controller
{
    /**
     * Get the authenticated user.
     */
    private function getAuthUser()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * Apply common query optimizations (Eager Loading & N+1 fixes).
     */
    protected function applyBaseQueryOptimizations($query)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('sanctum')->user();
        $userId = $user ? $user->id : null;

        return $query->withExists('likes')
            ->with([
                'media',
                'user' => function ($q) use ($userId) {
                    $q->with(['interests', 'media']); // Eager load media for profile_photo_url logic
                    if ($userId) {
                        $q->withExists([
                            'followers as is_followed_by_me' => function ($f) use ($userId) {
                                $f->where('follower_id', $userId);
                            }
                        ]);
                    }
                }
            ]);
    }

    /**
     * Mixed Feed (Home/Explore) - "For You" Logic.
     * Shows a mix of active posts based on engagement and randomness.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $query = UserPost::query();
            $posts = $this->applyBaseQueryOptimizations($query)
                ->forYou() // Algorithmic scope
                ->simplePaginate(15);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error("Feed Fetch Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the feed.'], 500);
        }
    }

    /**
     * Posts Feed.
     * Shows Images, Text, and Carousels sorted by trending score.
     */
    public function postsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $query = UserPost::query()
                ->whereIn('type', ['post', 'carousel', 'video']);

            $posts = $this->applyBaseQueryOptimizations($query)
                ->trending()
                ->simplePaginate(15);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error("Posts Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching posts.'], 500);
        }
    }

    /**
     * Videos Feed.
     * Shows only long-form videos sorted by engagement.
     */
    public function videosFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $query = UserPost::query()->ofType('video');

            $videos = $this->applyBaseQueryOptimizations($query)
                ->trending()
                ->simplePaginate(15);

            return PostResource::collection($videos);
        } catch (\Exception $e) {
            Log::error("Videos Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching videos.'], 500);
        }
    }

    /**
     * Reels Feed.
     * Shows short videos (reels) sorted by popularity.
     */
    public function reelsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $query = UserPost::query()->ofType('reel');

            $reels = $this->applyBaseQueryOptimizations($query)
                ->trending()
                ->simplePaginate(15);

            return PostResource::collection($reels);
        } catch (\Exception $e) {
            Log::error("Reels Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching reels.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // CRUD ACTIONS
    // -------------------------------------------------------------------------

    /**
     * Create a new post and handle media uploads.
     * Automatically sorts files into 'images' or 'videos' collections.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            $post = DB::transaction(function () use ($request, $user) {
                $uuid = Str::uuid();

                // Create Post Record
                $post = UserPost::create([
                    'user_id' => $user->id,
                    'uuid' => $uuid,
                    'type' => $request->type,
                    'caption' => $request->caption,
                    'visibility' => $request->visibility ?? 'public',
                    'status' => 'active',
                    'view_count' => 0,
                    'like_count' => 0,
                    'comment_count' => 0,
                ]);

                // Handle Media Uploads
                if ($request->hasFile('media')) {
                    $mediaFiles = $request->file('media');

                    if (!is_array($mediaFiles)) {
                        $mediaFiles = [$mediaFiles];
                    }

                    foreach ($mediaFiles as $file) {
                        // Detect MIME type on server side for accuracy
                        $mimeType = $file->getMimeType();

                        $collection = null;

                        // Assign collection based on file content, not post type.
                        // This matches the Model's validation rules.
                        if (str_starts_with($mimeType, 'image/')) {
                            $collection = 'images';
                        } elseif (str_starts_with($mimeType, 'video/')) {
                            $collection = 'videos';
                        }

                        // Upload if valid collection found
                        if ($collection) {
                            $media = $post->addMedia($file)->toMediaCollection($collection);

                            // Trigger background processing
                            if ($media instanceof Media) {
                                try {
                                    ProcessMediaJob::dispatch($media);
                                } catch (\Exception $e) {
                                    Log::error("Media Processing Job Failed: " . $e->getMessage());
                                }
                            }
                        } else {
                            Log::warning("Skipped file upload: Unsupported MIME type {$mimeType}");
                        }
                    }
                }
                return $post;
            });

            return response()->json([
                'message' => 'Post created successfully',
                'data' => new PostResource($post->load('media', 'user')),
            ], 201); // Created
        } catch (\Exception $e) {
            Log::error("Store Post Error: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to create post.',
                'debug_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function getPostsByUser(Request $request, $username): AnonymousResourceCollection|JsonResponse
    {
        try {
            // 1. Find the target user
            $targetUser = User::where('username', $username)->first();

            if (!$targetUser) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // 2. Identify the current logged-in user
            /** @var \App\Models\User|null $currentUser */
            $currentUser = Auth::guard('sanctum')->user();

            // 3. Privacy Check Logic
            $isMine = $currentUser && $currentUser->id === $targetUser->id;

            // Check following status
            $isFollowing = false;
            if ($currentUser && !$isMine) {
                $isFollowing = $currentUser->isFollowing($targetUser);
            }

            // If account is private and viewer is neither owner nor follower, deny access
            if ($targetUser->account_privacy === 'private' && !$isMine && !$isFollowing) {
                return response()->json([
                    'message' => 'This account is private. Follow to see their posts.',
                    'is_private' => true,
                    'posts' => []
                ], 403);
            }

            // 4. Build the query
            $query = UserPost::query()
                ->where('user_id', $targetUser->id);

            $query = $this->applyBaseQueryOptimizations($query);

            // 5. Apply filtering logic
            // Frontend sends: ?filter=posts OR ?filter=videos OR ?filter=all
            $filter = $request->input('filter', 'all');

            if ($filter === 'posts') {
                // Filter 1: Only Images and Carousels
                $query->whereIn('type', ['post', 'carousel']);
            } elseif ($filter === 'videos') {
                // Filter 2: Only Videos and Reels
                $query->whereIn('type', ['video', 'reel']);
            }

            // 6. Sorting & Pagination
            $posts = $query->orderBy('created_at', 'DESC')
                ->simplePaginate(12);

            // 7. Return Resource
            return PostResource::collection($posts)->additional([
                'meta' => [
                    'user_stats' => [
                        'post_count' => $targetUser->posts()->active()->count(), // Always show full count for active posts
                        'is_private' => $targetUser->account_privacy === 'private',
                        'is_following' => $isFollowing,
                        'current_filter' => $filter // Inform frontend about the applied filter
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("User Posts Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
    /**
     * Show a single post.
     */
    public function show(Request $request, UserPost $post, ViewTrackingService $viewService): JsonResponse
    {
        try {
            // $post->increment('view_count');
            $viewService->track($post, $request);
            return response()->json(new PostResource($post->load('media', 'user')));
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'You are not authorized to view this post.'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Post not found.'], 404);
        }
    }

    /**
     * Delete a post.
     */
    public function destroy(UserPost $post): JsonResponse
    {
        try {
            // $this->authorize('delete', $post);
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        } catch (\Exception $e) {
            Log::error("Delete Post Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // INTERACTIONS
    // -------------------------------------------------------------------------

    /**
     * Toggle Like/Unlike on a post.
     */
    public function toggleLike($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $user = $this->getAuthUser();

            $hasLiked = $post->likes()->where('user_id', $user->id)->exists();

            if ($hasLiked) {
                $likeService->unlike($post);
                $message = 'Post unliked.';
                $isLiked = false;
            } else {
                $likeService->like($post);
                $message = 'Post liked.';
                $isLiked = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'is_liked' => $isLiked,
                    'likes_count' => $likeService->likeCount($post)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Like Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 400);
        }
    }

    /**
     * Report a post.
     */

    public function reportPost(Request $request, $id, ReportService $reportService): JsonResponse
    {
        // ✅ Laravel handles validation errors automatically

        try {
            $request->validate([
                'reason' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->errors(),
            ], 422);
        }

        $post = UserPost::findOrFail($id);
        $user = $this->getAuthUser();

        $alreadyReported = $post->reports()
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this post.'
            ], 409);
        }

        $reportService->report(
            $post,
            $user,
            $request->reason,
            $request->description
        );

        return response()->json([
            'success' => true,
            'message' => 'Post reported successfully.',
            'data' => [
                'reports_count' => $reportService->reportCount($post)
            ]
        ]);
    }

    /**
     * Global Trending Feed.
     * Shows all active posts sorted by trending score and freshness.
     */
    public function globalTrendingFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $seed = $request->input('seed') ?: time();

            $query = UserPost::query();

            $posts = $this->applyBaseQueryOptimizations($query)
                ->orderByRaw("RAND($seed)")
                ->simplePaginate(15);

            return PostResource::collection($posts)->additional([
                'meta' => [
                    'seed' => (int) $seed,
                    'message' => 'Pass this seed to the next page to maintain order.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Global Trending Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching trending feed.'], 500);
        }
    }

    /**
     * Trending Feed based on User Interests.
     * Shows posts from users who share interests with the authenticated user.
     * Fallback to global trending if no results found.
     */
    public function trendingInterestsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $seed = $request->input('seed') ?: time();

            /** @var \App\Models\User|null $user */
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return $this->globalTrendingFeed($request);
            }

            $interestIds = $user->interests()->pluck('interests.id');

            if ($interestIds->isEmpty()) {
                return $this->globalTrendingFeed($request);
            }

            // Find other users with these interests
            $relatedUserIds = DB::table('interest_user')
                ->whereIn('interest_id', $interestIds)
                ->where('user_id', '!=', $user->id)
                ->pluck('user_id')
                ->unique();

            if ($relatedUserIds->isEmpty()) {
                return $this->globalTrendingFeed($request);
            }

            $query = UserPost::query()->whereIn('user_id', $relatedUserIds);

            $posts = $this->applyBaseQueryOptimizations($query)
                ->orderByRaw("RAND($seed)")
                ->simplePaginate(15);

            // If the interest-based feed is empty (on the first page), fallback to global
            if ($posts->isEmpty() && (int) $request->input('page', 1) === 1) {
                return $this->globalTrendingFeed($request);
            }

            return PostResource::collection($posts)->additional([
                'meta' => [
                    'seed' => (int) $seed,
                    'message' => 'Pass this seed to the next page to maintain order.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Interests Trending Feed Error: " . $e->getMessage());
            // Always fallback to global on error to ensure user sees something
            return $this->globalTrendingFeed($request);
        }
    }

    /**
     * "For You" Feed.
     * Interest-based shuffling with seeded randomness to prevent duplicates on pagination.
     */
    public function forYouFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            // Seed management: Either use provided seed or generate new one for first page
            $seed = $request->input('seed') ?: time();

            /** @var \App\Models\User|null $user */
            $user = Auth::guard('sanctum')->user();

            $query = UserPost::query();

            // Filter by interests if user has them
            if ($user) {
                $interestIds = $user->interests()->pluck('interests.id');
                if ($interestIds->isNotEmpty()) {
                    $relatedUserIds = DB::table('interest_user')
                        ->whereIn('interest_id', $interestIds)
                        ->pluck('user_id')
                        ->unique();

                    if ($relatedUserIds->isNotEmpty()) {
                        $query->whereIn('user_id', $relatedUserIds);
                    }
                }
            }

            // Apply optimizations and seeded shuffle
            $posts = $this->applyBaseQueryOptimizations($query)
                ->orderByRaw("RAND($seed)")
                ->simplePaginate(15);

            // Fallback to global shuffle if interest-based feed is empty on Page 1
            if ($posts->isEmpty() && (int) $request->input('page', 1) === 1) {
                $query = UserPost::query();
                $posts = $this->applyBaseQueryOptimizations($query)
                    ->orderByRaw("RAND($seed)")
                    ->simplePaginate(15);
            }

            return PostResource::collection($posts)->additional([
                'meta' => [
                    'seed' => (int) $seed,
                    'message' => 'Pass this seed to the next page to maintain order.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("For You Feed Error: " . $e->getMessage());
            // Fallback to global trending on error for stability
            return $this->globalTrendingFeed($request);
        }
    }
}
