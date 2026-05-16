<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\UserPost;
use App\Services\CommentService;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * List comments for a post (Top level only).
     */
    public function postComments(string $postId): JsonResponse
    {
        try {
            $post = UserPost::find($postId);

            if (!$post) {
                return response()->json(['success' => false, 'message' => 'Post not found.'], 404);
            }

            // Fetch top-level comments with replies and user data
            $comments = $post->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user', 'replies.likes'])
                ->withCount('replies')
                ->latest()
                ->simplePaginate(30);

            return response()->json([
                'success' => true,
                'message' => 'Comments fetched successfully.',
                'data' => CommentResource::collection($comments)
            ], 200);
        } catch (Exception $e) {
            return $this->handleError($e, 'fetching comments');
        }
    }

    /**
     * Store a new comment.
     * Fix: Resolves model first to ensure correct Polymorphic Type storage.
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        try {
            $commentableType = $request->route('commentable_type');
            $commentableId   = $request->route('commentable_id');
            $parentId        = $request->route('parent_id');

            // 1. Resolve and Validate Target Model
            $modelClass = $this->commentService->resolveModelClass($commentableType);
            if (!$modelClass) {
                return response()->json(['success' => false, 'message' => 'Invalid resource type.'], 400);
            }

            $targetModel = $modelClass::find($commentableId);
            if (!$targetModel) {
                return response()->json(['success' => false, 'message' => 'Content not found.'], 404);
            }

            // 2. Add Comment via Service
            // We pass the Model instance so Laravel handles the morph_type correctly
            $comment = $this->commentService->addComment($targetModel, $request->validated(), $parentId);

            // 3. Load relationships for immediate UI update
            $comment->load(['user', 'replies', 'likes']);

            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'data'    => new CommentResource($comment)
            ], 201);
        } catch (Exception $e) {
            return $this->handleError($e, 'posting comment');
        }
    }

    /**
     * Toggle Like.
     */
    public function toggleCommentLike(string $commentId, LikeService $likeService): JsonResponse
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return response()->json(['success' => false, 'message' => 'Comment not found.'], 404);
            }

            // Toggle like logic
            $hasLiked = $likeService->hasLiked($comment);

            if ($hasLiked) {
                $likeService->unlike($comment);
                $message = 'Comment unliked.';
                $likedState = false;
            } else {
                $likeService->like($comment);
                $message = 'Comment liked.';
                $likedState = true;
            }

            // Get fresh count directly from DB
            $likesCount = $comment->likes()->count();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'liked'       => $likedState,
                    'likes_count' => $likesCount
                ]
            ], 200);
        } catch (Exception $e) {
            return $this->handleError($e, 'toggling like');
        }
    }

    /**
     * Delete Comment.
     */
    public function destroy(string $commentId): JsonResponse
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return response()->json(['success' => false, 'message' => 'Comment not found.'], 404);
            }

            $this->authorize('delete', $comment);

            $this->commentService->deleteComment($comment);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        } catch (Exception $e) {
            return $this->handleError($e, 'deleting comment');
        }
    }

    private function handleError(Exception $e, string $context): JsonResponse
    {
        \Log::error("Error {$context}: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : "Error {$context}."
        ], 500);
    }
}
