<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{
     public function showPostDetails($id)
    {
        try {
            // Find the post by ID
            $post = Post::with(['likes.user', 'comments.user']) // Eager load likes and comments along with users
                ->findOrFail($id);

            // Get the total likes for the post
            $totalLikes = $post->likes()->count();

            // Get all comments with their user info
            $comments = $post->comments()->with('user')->get();

            // Prepare post details
            $postDetails = [
                'post_id' => $post->id,
                'caption' => $post->caption,
                'media_metadata' => $post->media_metadata,
                'type' => $post->type,
                'status' => $post->status,
                'visibility' => $post->visibility,
                'created_at' => $post->created_at,
                'total_likes' => $totalLikes,
                'total_comments' => $comments->count(),
                'likes' => $post->likes->map(function ($like) {
                    return [
                        'user_id' => $like->user_id,
                        'username' => $like->user->name,
                        'profile_picture' => $like->user->profile_picture ?? null, // Assuming the user model has a profile picture field
                    ];
                }),
                'comments' => $comments->map(function ($comment) {
                    return [
                        'comment_id' => $comment->id,
                        'content' => $comment->content,
                        'user_id' => $comment->user_id,
                        'username' => $comment->user->name,
                        'profile_picture' => $comment->user->profile_picture ?? null, // Assuming the user model has a profile picture field
                        'created_at' => $comment->created_at,
                    ];
                }),
            ];

            return response()->json(['post_details' => $postDetails]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Feed - Get all posts with comments and likes
    // public function index()
    // {
    //     // $user = Auth::user();

    //     // Get posts for the logged-in user
    //     $posts = Post::with(['user', 'comments', 'likes'])
    //         ->orderByDesc('created_at')
    //         ->get();

    //     return $posts;
    //     // return view('admin.post.index',compact('posts'));
    // }
// public function index()
// {
//     // Get posts for the logged-in user with like and comment counts
//     $posts = Post::withCount(['likes', 'comments']) // Efficiently get the like and comment counts
//         ->orderByDesc('created_at')  // Order posts by creation date
//         ->get();  // Get the posts

//     // Return the posts as a JSON response
//     return response()->json($posts);
// }
public function index()
{
    // Get all posts with relationships (likes, comments, user) and order by created_at
    $posts = Post::with(['user', 'comments.user', 'likes.user'])
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($post) {
            // Decode media_metadata if it's stored as a string
            $media = is_string($post->media_metadata) ? json_decode($post->media_metadata, true) : $post->media_metadata;

            // Add the like and comment counts
            $post->like_count = $post->likes()->count();  // Count the number of likes
            $post->comment_count = $post->comments()->count();  // Count the number of comments

            // Get the users who liked and commented
            $post->liked_by = $post->likes->pluck('user.name');  // List of users who liked the post
            $post->commented_by = $post->comments->map(function ($comment) {
                return [
                    'user' => $comment->user->name,
                    'comment' => $comment->content,  // Assuming `content` is the comment text
                    'like_count' => $comment->likes()->count(),  // Count of likes on the comment
                    'comment_id' => $comment->id  // To delete the comment later
                ];
            });

            // Also add the media to the post
            $post->media = $media;  // Attach the media information to the post

            return $post;
        });

    // Return view with the posts data
    return view('admin.post.index', compact('posts'));
}

public function deleteComment($commentId)
{
    // Find the comment by ID and delete it
    $comment = Comment::findOrFail($commentId);
    $comment->delete();

    return response()->json(['message' => 'Comment deleted successfully!']);
}




}
