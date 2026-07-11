<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\UserPost;
use App\Models\User;
use App\Services\LikeService;
use App\Services\ReportService;
use App\Services\PostMediaService;
use App\Services\PostFeedService;
use App\Services\ViewTrackingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Services\UserPostService;

class UserPostController extends Controller
{
    public function __construct(
        private PostFeedService    $feedService,
        private PostMediaService   $mediaService,
        private LikeService        $likeService,
        private ReportService      $reportService,
        private ViewTrackingService $viewService,
        private UserPostService    $postService,
    ) {}

    private function authUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    // -------------------------------------------------------------------------
    // FEEDS
    // -------------------------------------------------------------------------

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            return PostResource::collection($this->feedService->mixedFeed());
        } catch (\Exception $e) {
            Log::error("Feed Fetch Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the feed.'], 500);
        }
    }

    public function postsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            return PostResource::collection($this->feedService->postsFeed());
        } catch (\Exception $e) {
            Log::error("Posts Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching posts.'], 500);
        }
    }

    public function videosFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            return PostResource::collection($this->feedService->videosFeed());
        } catch (\Exception $e) {
            Log::error("Videos Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching videos.'], 500);
        }
    }

    public function reelsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            return PostResource::collection($this->feedService->reelsFeed());
        } catch (\Exception $e) {
            Log::error("Reels Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching reels.'], 500);
        }
    }

    public function getRelatedVideos(Request $request, $id): AnonymousResourceCollection|JsonResponse
    {
        try {
            return PostResource::collection($this->feedService->getRelatedVideos($id));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Related Videos Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching related videos.'], 500);
        }
    }

    public function globalTrendingFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $seed = $request->input('seed') ?: time();

            $posts = $this->feedService->globalTrendingFeed($seed);

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

    public function trendingInterestsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $seed = $request->input('seed') ?: time();

            $posts = $this->feedService->trendingInterestsFeed($request);

            return PostResource::collection($posts)->additional([
                'meta' => [
                    'seed' => (int) $seed,
                    'message' => 'Pass this seed to the next page to maintain order.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Interests Trending Feed Error: " . $e->getMessage());
            return $this->globalTrendingFeed($request);
        }
    }

    public function forYouFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $seed = $request->input('seed') ?: time();

            $posts = $this->feedService->forYouFeed($request);

            return PostResource::collection($posts)->additional([
                'meta' => [
                    'seed' => (int) $seed,
                    'message' => 'Pass this seed to the next page to maintain order.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("For You Feed Error: " . $e->getMessage());
            return $this->globalTrendingFeed($request);
        }
    }

    // -------------------------------------------------------------------------
    // CRUD
    // -------------------------------------------------------------------------

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user = $this->authUser();

            $post = $this->postService->createPost(
                $user,
                $request->only(['type', 'caption', 'visibility']),
                $request->file('media')
            );

            return response()->json([
                'message' => 'Post created successfully',
                'data' => new PostResource($post->load('media', 'user')),
            ], 201);
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

    public function show(Request $request, UserPost $post): JsonResponse
    {
        try {
            $this->viewService->track($post, $request);
            return response()->json(new PostResource($post->load('media', 'user')));
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'You are not authorized to view this post.'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Post not found.'], 404);
        }
    }

    public function destroy(UserPost $post): JsonResponse
    {
        try {
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        } catch (\Exception $e) {
            Log::error("Delete Post Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post.'], 500);
        }
    }

    public function getPostsByUser(Request $request, $username): AnonymousResourceCollection|JsonResponse
    {
        try {
            $targetUser = User::where('username', $username)->first();

            if (!$targetUser) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            /** @var User|null $currentUser */
            $currentUser = Auth::guard('sanctum')->user();

            $isMine = $currentUser && $currentUser->id === $targetUser->id;

            if ($currentUser && !$isMine && $currentUser->hasBlockRelationWith($targetUser)) {
                $isBlockedByMe = $currentUser->hasBlocked($targetUser);
                return response()->json([
                    'message' => $isBlockedByMe
                        ? 'You have blocked this user.'
                        : 'This user is not available.',
                    'is_blocked' => true,
                    'is_blocked_by_me' => $isBlockedByMe,
                    'user' => [
                        'id' => $targetUser->id,
                        'name' => $targetUser->name,
                        'username' => $targetUser->username,
                        'avatar' => $targetUser->profile_photo_url,
                    ],
                    'posts' => [],
                    'meta' => [
                        'user_stats' => [
                            'post_count' => 0,
                            'is_private' => false,
                            'is_following' => false,
                            'is_blocked' => true,
                            'current_filter' => $request->input('filter', 'all'),
                        ]
                    ]
                ], 200);
            }

            $isFollowing = false;
            if ($currentUser && !$isMine) {
                $isFollowing = $currentUser->isFollowing($targetUser);
            }

            if ($targetUser->account_privacy === 'private' && !$isMine && !$isFollowing) {
                return response()->json([
                    'message' => 'This account is private. Follow to see their posts.',
                    'is_private' => true,
                    'posts' => []
                ], 403);
            }

            $filter = $request->input('filter', 'all');
            $posts = $this->feedService->getUserPosts($targetUser->id, $filter);

            return PostResource::collection($posts)->additional([
                'meta' => [
                    'user_stats' => [
                        'post_count' => $targetUser->posts()->active()->count(),
                        'is_private' => $targetUser->account_privacy === 'private',
                        'is_following' => $isFollowing,
                        'is_blocked' => false,
                        'current_filter' => $filter,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("User Posts Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // INTERACTIONS
    // -------------------------------------------------------------------------

    public function toggleLike($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $user = $this->authUser();
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
                    'likes_count' => $likeService->likeCount($post),
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Like Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 400);
        }
    }

    public function reportPost(Request $request, $id, ReportService $reportService): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            $post = UserPost::findOrFail($id);
            $user = $this->authUser();
            $alreadyReported = $post->reports()
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyReported) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reported this post.'
                ], 409);
            }

            $reportService->report($post, $user, $request->reason, $request->description);

            return response()->json([
                'success' => true,
                'message' => 'Post reported successfully.',
                'data' => [
                    'reports_count' => $reportService->reportCount($post),
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Report Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 400);
        }
    }
}
