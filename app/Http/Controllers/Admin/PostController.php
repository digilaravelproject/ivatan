<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{
// app/Http/Controllers/PostController.php

public function showPostDetails($id)
{
    try {
        // Fetch the post with related data (likes, comments, user)
        $post = Post::with(['user', 'likes.user', 'comments.user'])
            ->findOrFail($id);

        // Prepare the post data for the view
        $postDetails = $this->preparePostDetails($post);

        // Fetch the list of possible status and visibility options
        $statuses = ['active', 'deleted', 'flagged'];
        $visibilities = ['public', 'private', 'friends'];

        // Return the view with the post details
        return view('admin.post.details', compact('postDetails', 'statuses', 'visibilities'));
    } catch (\Exception $e) {
        // Handle any exceptions and display a user-friendly error message
        return back()->with('error', 'Post not found or an error occurred.');
    }
}

// Method to handle updates (called when the admin updates status/visibility)
public function updatePostDetails(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:active,deleted,flagged',
        'visibility' => 'required|in:public,private,friends',
    ]);

    try {
        $post = Post::findOrFail($id);
        $post->status = $request->status;
        $post->visibility = $request->visibility;
        $post->save();

        return redirect()->route('admin.post.details', $id)->with('success', 'Post updated successfully.');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to update post.');
    }
}



private function preparePostDetails(Post $post)
{
    // Get total likes
    $totalLikes = $post->likes()->count();

    // Get total comments
    $totalComments = $post->comments()->count();

    // Map likes to a more readable format
    $likes = $post->likes->map(function ($like) {
        return [
            'user_id' => $like->user_id,
            'username' => $like->user->name,
            'profile_picture' => $like->user->profile_picture ?? asset('images/default-avatar.png'),
        ];
    });

    // Map comments to a more readable format
    $comments = $post->comments->map(function ($comment) {
        return [
            'comment_id' => $comment->id,
            'content' => $comment->content,
            'username' => $comment->user->name,
            'profile_picture' => $comment->user->profile_picture ?? asset('images/default-avatar.png'),
            'created_at' => $comment->created_at,
        ];
    });

    // Check if the post has media (images or videos)
    $media = [
        'images' => isset($post->media_metadata['images']) ? $post->media_metadata['images'] : [],
        'videos' => isset($post->media_metadata['videos']) ? $post->media_metadata['videos'] : []
    ];

    // Return the formatted data
    return [
        'post_id' => $post->id,
        'caption' => $post->caption,
        'media_metadata' => $media,
        'type' => $post->type,
        'status' => $post->status,
        'visibility' => $post->visibility,
        'created_at' => $post->created_at,
        'total_likes' => $totalLikes,
        'total_comments' => $totalComments,
        'likes' => $likes,
        'comments' => $comments,
        'user' => $post->user,  // Pass the post's user for profile info
    ];
}



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


    public function getLikes($id)
    {
        $post = Post::with('likes.user')->findOrFail($id);
        return response()->json($post->likes->map(function ($like) {
            return [
                'id' => $like->id,
                'user_id' => $like->user_id,
                'username' => $like->user->name,
                'profile_picture' => $like->user->profile_picture ?? asset('images/default-avatar.png'),
            ];
        }));
    }

    public function getComments($id)
    {
        $post = Post::with('comments.user')->findOrFail($id);
        return response()->json($post->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'username' => $comment->user->name,
                'profile_picture' => $comment->user->profile_picture ?? asset('images/default-avatar.png'),
                'content' => $comment->content,
                'like_count' => $comment->likes()->count(),
                'created_at' => $comment->created_at->diffForHumans(),
            ];
        }));
    }

    public function showLikes($id)
    {
        $post = Post::with('likes.user')->findOrFail($id);

        return view('admin.post.likes', compact('post'));
    }

    public function showComments($id)
    {
        $post = Post::with('comments.user')->findOrFail($id);

        return view('admin.post.comments', compact('post'));
    }
}
