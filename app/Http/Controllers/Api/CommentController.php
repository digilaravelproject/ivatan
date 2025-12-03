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
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * ✅ List comments for a UserPost (Instagram Style)
     * Returns top-level comments with their replies nested.
     */
    public function postComments(string $postId): JsonResponse
    {
        try {
            // 1. Find the post manually to ensure 404 handling is clean
            $post = UserPost::find($postId);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found.'
                ], 404);
            }

            // 2. Fetch Comments
            // We use withCount('replies') to show "View X replies" button in frontend
            $comments = $post->comments()
                ->whereNull('parent_id') // Top level only
                ->with(['user', 'replies.user', 'replies.likes']) // Eager load nested data
                ->withCount('replies') // Efficiently count replies
                ->latest()
                ->get(); // You can change to ->paginate(10) for infinite scroll

            // 3. Return formatted resource
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
     * ✅ Store a new comment or reply
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        try {
            // 1. Validate inputs (Route params + Body)
            // Note: StoreCommentRequest validates 'body'. We validate logic here.

            $commentableType = $request->route('commentable_type');
            $commentableId = $request->route('commentable_id');
            $parentId = $request->route('parent_id');

            // 2. Normalize Commentable Type (e.g., "post" -> "App\Models\UserPost")
            $modelClass = $this->commentService->resolveModelClass($commentableType);

            if (!$modelClass || !class_exists($modelClass)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid resource type provided.'
                ], 400);
            }

            // 3. Verify the Target Exists
            $targetModel = $modelClass::find($commentableId);
            if (!$targetModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'The post/content you are trying to comment on does not exist.'
                ], 404);
            }

            // 4. Verify Parent Comment (if this is a reply)
            if ($parentId) {
                $parentComment = Comment::find($parentId);
                if (!$parentComment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The comment you are replying to has been deleted.'
                    ], 404);
                }
            }

            // 5. Prepare Data
            $data = array_merge($request->validated(), [
                'commentable_type' => $modelClass,
                'commentable_id'   => $commentableId,
                'parent_id'        => $parentId,
            ]);

            // 6. Create via Service
            $comment = $this->commentService->addComment($data);

            // 7. Load relationships for the response
            $comment->load(['user', 'replies', 'likes']);

            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'data' => new CommentResource($comment)
            ], 201);
        } catch (Exception $e) {
            return $this->handleError($e, 'posting comment');
        }
    }

    /**
     * ✅ Toggle Like/Unlike on a comment
     */
    public function toggleCommentLike(string $commentId, LikeService $likeService): JsonResponse
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found.'
                ], 404);
            }

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

            // Refresh count
            $likesCount = $likeService->likeCount($comment);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'liked' => $likedState,
                    'likes_count' => $likesCount
                ]
            ], 200);
        } catch (Exception $e) {
            return $this->handleError($e, 'toggling like');
        }
    }

    /**
     * ✅ Delete a comment
     */
    public function destroy(string $commentId): JsonResponse
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found.'
                ], 404);
            }

            // Authorization Check
            $this->authorize('delete', $comment);

            $this->commentService->deleteComment($comment);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this comment.'
            ], 403);
        } catch (Exception $e) {
            return $this->handleError($e, 'deleting comment');
        }
    }

    /**
     * 🛠 Helper: Centralized Error Handler
     */
    private function handleError(Exception $e, string $context): JsonResponse
    {
        // Log the error internally
        \Log::error("Error while {$context}: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        // Return generic message to user for security, unless in debug mode
        $message = config('app.debug') ? $e->getMessage() : "Something went wrong while {$context}. Please try again later.";

        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }
}
