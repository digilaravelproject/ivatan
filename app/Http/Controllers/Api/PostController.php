<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'caption' => 'required|string|max:255',
                'images' => 'required|array', // Changed to 'media' instead of 'images' to cover all types
                'images.*' => 'mimes:jpeg,png,jpg,gif,svg,mp4|max:2048', // Validates images and videos
            ]);

            $user = Auth::user();

            // Generate UUID
            $uuid = Str::uuid();

            // Automatically detect post type (image, video, carousel)
            $postType = 'image';  // Default type
            $mediaUrls = [];

            // Loop through the media files to store them and determine the type
            $isVideo = false;
            foreach ($request->images as $media) {
                $extension = $media->getClientOriginalExtension();

                // If it's a video, change the post type
                if (in_array($extension, ['mp4', 'avi', 'mov'])) {
                    $isVideo = true;
                }

                $path = $media->store('posts', 'public'); // Store the file locally in public/posts directory
                $mediaUrls[] = 'posts/' . basename($path); // Store relative path instead of full URL

                if (count($request->images) > 1) {
                    $postType = 'carousel';
                }
            }

            if ($isVideo) {
                $postType = 'video';
            }

            // Create the post with UUID and type
            $post = Post::create([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'caption' => $request->caption,
                'status' => 'active',
                'visibility' => 'public',
                'type' => $postType,  // Set the type dynamically
            ]);

            // Store media metadata (images or video URLs)
            $post->setMediaMetadata([
                'images' => $mediaUrls,  // Store all media URLs (images or video)
                'likes' => 0,
                'comments' => []
            ]);

            return response()->json(['message' => 'Post created successfully.', 'post' => $post]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Like a Post
    public function like($id)
    {
        try {
            $user = Auth::user(); // Get the authenticated user
            $post = Post::findOrFail($id); // Find the post

            // Check if the user has already liked this post
            $existingLike = $post->likes()->where('user_id', $user->id)->first();

            if ($existingLike) {
                return response()->json(['error' => 'You have already liked this post.'], 400);
            }

            // If not, create a new like record
            $like = new Like([
                'user_id' => $user->id,
                'likeable_type' => Post::class,
                'likeable_id' => $post->id,
            ]);
            $like->save(); // Save the like to the database

            // Update the likes count in the media metadata
            $media = $post->media_metadata;
            if (!isset($media['likes'])) {
                $media['likes'] = 0;
            }
            $media['likes'] += 1; // Increment the like count
            $post->setMediaMetadata($media); // Save the updated media metadata

            return response()->json(['message' => 'Post liked successfully.', 'likes' => $media['likes']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function unlike($id)
    {
        try {
            $user = Auth::user(); // Get the authenticated user
            $post = Post::findOrFail($id); // Find the post

            // Check if the user has already liked this post
            $existingLike = $post->likes()->where('user_id', $user->id)->first();

            if (!$existingLike) {
                return response()->json(['error' => 'You have not liked this post yet.'], 400);
            }

            // If the like exists, delete it from the likes table
            $existingLike->delete(); // Remove the like from the likes table

            // Update the likes count in the media metadata
            $media = $post->media_metadata;
            if (isset($media['likes']) && $media['likes'] > 0) {
                $media['likes'] -= 1; // Decrement the like count
            }
            $post->setMediaMetadata($media); // Save the updated media metadata

            return response()->json(['message' => 'Post unliked successfully.', 'likes' => $media['likes']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Add a Comment to a Post
    public function comment(Request $request, $id)
    {
        try {
            $user = Auth::user(); // Get the authenticated user
            $post = Post::findOrFail($id); // Find the post to which the comment is being added

            // Validate the request
            $request->validate([
                'comment' => 'required|string|max:255',
            ]);

            // Add the comment to the post
            $comment = new Comment([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'content' => $request->comment,
                'status' => 'active', // Assuming a status field for comments (active, deleted, flagged)
            ]);
            $comment->save(); // Save the comment to the database

            return response()->json(['message' => 'Comment added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Remove a Comment
    public function deleteComment($commentId)
    {
        try {
            $user = Auth::user(); // Get the authenticated user
            $comment = Comment::findOrFail($commentId); // Find the comment by its ID

            // Check if the user is the owner of the comment
            if ($comment->user_id !== $user->id) {
                return response()->json(['error' => 'You cannot delete someone else\'s comment.'], 400);
            }

            // Mark the comment as deleted (or you could just delete it)
            $comment->status = 'deleted'; // Change status to 'deleted'
            $comment->save(); // Save the updated status

            return response()->json(['message' => 'Comment deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function likeComment($commentId)
    {
        try {
            $user = Auth::user(); // Get the authenticated user
            $comment = Comment::findOrFail($commentId); // Find the comment

            // Check if the user already liked this comment
            $existingLike = $comment->likes()->where('user_id', $user->id)->first();
            if ($existingLike) {
                return response()->json(['error' => 'You have already liked this comment.'], 400);
            }

            // If not, create a new like record for the comment
            $like = new Like([
                'user_id' => $user->id,
                'likeable_type' => Comment::class,
                'likeable_id' => $comment->id,
            ]);
            $like->save(); // Save the like to the database

            // Update the like count in the comment (or post if needed)
            $media = $comment->post->media_metadata;
            if (!isset($media['likes'])) {
                $media['likes'] = 0;
            }
            $media['likes'] += 1; // Increment like count
            $comment->post->setMediaMetadata($media); // Save the updated metadata

            return response()->json(['message' => 'Comment liked successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function unlikeComment($commentId)
    {
        try {
            $user = Auth::user(); // Get the authenticated user
            $comment = Comment::findOrFail($commentId); // Find the comment

            // Check if the user already liked this comment
            $existingLike = $comment->likes()->where('user_id', $user->id)->first();
            if (!$existingLike) {
                return response()->json(['error' => 'You have not liked this comment yet.'], 400);
            }

            // Remove the like from the likes table
            $existingLike->delete(); // Delete the like

            // Update the like count in the post’s media metadata
            $media = $comment->post->media_metadata;
            if (isset($media['likes']) && $media['likes'] > 0) {
                $media['likes'] -= 1; // Decrement like count
            }
            $comment->post->setMediaMetadata($media); // Save the updated metadata

            return response()->json(['message' => 'Comment unliked successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


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
    public function feed()
    {
        $user = Auth::user();

        // Get posts for the logged-in user
        $posts = Post::where('user_id', $user->id)
            ->with(['user', 'comments', 'likes'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($posts);
    }
}
