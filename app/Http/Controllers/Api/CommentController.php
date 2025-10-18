<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\UserPost;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * ✅ List comments with replies on a post/reel/etc.
     * Get all top-level comments for a specific model (e.g., Post, Video).
     */
    // public function index(string $commentableType, int $commentableId): JsonResponse
    // {
    //     $modelClass = 'App\\Models\\' . $commentableType;

    //     if (!class_exists($modelClass)) {
    //         return response()->json(['error' => 'Invalid commentable type.'], 400);
    //     }

    //     $model = $modelClass::findOrFail($commentableId);

    //     $comments = $model->comments()
    //         ->whereNull('parent_id')
    //         ->with(['user', 'replies.user', 'likes', 'replies.likes'])
    //         ->latest()
    //         ->get();
    //     if (!$model) {
    //         return response()->json(['error' => 'Resource not found.'], 404);
    //     }
    //     return response()->json($comments);
    // }

    public function postComments(UserPost $post): JsonResponse
    {

        return $this->getCommentsFor($post);
    }

    // public function videoComments(Video $video): JsonResponse
    // {
    //     return $this->getCommentsFor($video);
    // }

    // public function productComments(Product $product): JsonResponse
    // {
    //     return $this->getCommentsFor($product);
    // }

    protected function getCommentsFor($model): JsonResponse
    {
        $comments = $model->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'likes', 'replies.likes'])
            ->latest()
            ->get();

        return response()->json($comments);
    }

    // ✅ Store comment or reply
    public function store_old(StoreCommentRequest $request, CommentService $commentService): JsonResponse
    {
        $comment = $commentService->addComment($request->validated());


        return response()->json([
            'message' => 'Comment posted',
            'data' => $comment->load('user', 'replies', 'likes')
        ], 201);
    }


    public function store(StoreCommentRequest $request, CommentService $commentService): JsonResponse
    {
        try {
            // Validate if the required parameters exist in the route
            $commentableType = $request->route('commentable_type');
            $commentableId = $request->route('commentable_id');
            $parentId = $request->route('parent_id') ?? null;

            // Check if all the necessary parameters are provided
            if (!$commentableType || !$commentableId) {
                return response()->json([
                    'message' => 'Commentable type or ID not found'
                ], 404); // Return 404 if commentable_type or commentable_id is missing
            }

            // Check if parentId is invalid or not found
            if ($parentId && !Comment::find($parentId)) {
                return response()->json([
                    'message' => 'Parent comment not found'
                ], 404); // Return 404 if parent comment is not found
            }

            // Merging the validated request data with the route parameters
            $data = array_merge($request->validated(), [
                'commentable_type' => $commentableType,
                'commentable_id' => $commentableId,
                'parent_id' => $parentId,
            ]);

            // Create the comment using the merged data
            $comment = $commentService->addComment($data);

            // Return a JSON response with the created comment and related data
            return response()->json([
                'message' => 'Comment posted successfully',
                'data' => $comment->load('user', 'replies', 'likes') // Include user, replies, and likes relationships
            ], 201);
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return a generic error message
            return response()->json([
                'message' => $e->getMessage()
            ], 400); // Return 400 for any other errors
        }
    }
    // ✅ Like or unlike a comment


    public function toggleCommentLike(Comment $comment, LikeService $likeService): JsonResponse
    {
        try {
            // Check if the user has already liked the comment
            if ($likeService->hasLiked($comment)) {
                // If liked, unlike the comment
                $likeService->unlike($comment);
                $message = 'Unliked';
                $liked = false;
            } else {
                // If not liked, like the comment
                $likeService->like($comment);
                $message = 'Liked';
                $liked = true;
            }

            // Get the updated like count
            $likesCount = $likeService->likeCount($comment);

            return response()->json([
                'message' => $message,
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'likes_count' => $likeService->likeCount($comment),
            ], 400); // Handle errors (e.g., already liked, etc.)
        }
    }



    // ✅ Delete a comment
    public function destroy(Comment $comment, CommentService $commentService): JsonResponse
    {
        $this->authorize('delete', $comment);
        // Optional: Restrict ownership

        $commentService->deleteComment($comment);

        return response()->json(['message' => 'Comment deleted']);
    }
}
