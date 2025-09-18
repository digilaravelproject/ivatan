<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use App\Models\Comment;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'post');

        $posts = UserPost::with(['user', 'comments.user', 'likes.user', 'media'])
            ->withCount(['likes', 'comments'])
            ->where('type', $type)
            ->active()
            ->latest()
            ->paginate(20);

        // Format posts for view if needed (e.g. attach media URLs)
        $posts->getCollection()->transform(function ($post) {
            // You can map media here if needed, but Spatie media is accessible via $post->getMedia()
            $post->images = $post->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ];
            });

            $post->videos = $post->getMedia('videos')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ];
            });

            return $post;
        });

        return view('admin.post.index', compact('posts'));
    }

    public function show($id)
    {
        $post = UserPost::with(['user', 'comments.user', 'likes.user', 'media'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);

        $postDetails = $this->preparePostDetails($post);

        $statuses = ['active', 'inactive', 'pending'];
        $visibilities = ['public', 'private'];

        // return view('admin.post.details', compact('postDetails', 'statuses', 'visibilities'));
        return response()->json([
            'postDetails' => $postDetails,
            'statuses' => $statuses,
            'visibilities' => $visibilities,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending',
            'visibility' => 'required|in:public,private',
        ]);

        try {
            $post = UserPost::findOrFail($id);
            $post->status = $request->status;
            $post->visibility = $request->visibility;
            $post->save();

            return redirect()->route('admin.userpost.details', $id)
                ->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update post.');
        }
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully!']);
    }

    public function getLikes($id)
    {
        $post = UserPost::with('likes.user')->findOrFail($id);

        return response()->json($post->likes->map(function ($like) {
            return [
                'id' => $like->id,
                'user_id' => $like->user_id,
                'username' => $like->user->name,
                'profile_picture' => $like->user->profile_photo_path ?? asset('images/default-avatar.png'),
            ];
        }));
    }

    public function getComments($id)
    {
        $post = UserPost::with('comments.user')->findOrFail($id);

        return response()->json($post->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'username' => $comment->user->name,
                'profile_picture' => $comment->user->profile_photo_path ?? asset('images/default-avatar.png'),
                'content' => $comment->body,
                'like_count' => $comment->likes()->count(),
                'created_at' => $comment->created_at->diffForHumans(),
            ];
        }));
    }

    public function showLikes($id)
    {
        $post = UserPost::with('likes.user')->findOrFail($id);

        return view('admin.post.likes', compact('post'));
    }

    public function showComments($id)
    {
        $post = UserPost::with('comments.user')->findOrFail($id);

        return view('admin.post.comments', compact('post'));
    }

    private function preparePostDetails(UserPost $post)
    {
        $totalLikes = $post->likes()->count();
        $totalComments = $post->comments()->count();

        $likes = $post->likes->map(function ($like) {
            return [
                'user_id' => $like->user_id,
                'username' => $like->user->name,
                'profile_picture' => $like->user->profile_photo_path ?? asset('images/default-avatar.png'),
            ];
        });

        $comments = $post->comments->map(function ($comment) {
            return [
                'comment_id' => $comment->id,
                'content' => $comment->body,
                'username' => $comment->user->name,
                'profile_picture' => $comment->user->profile_photo_path ?? asset('images/default-avatar.png'),
                'created_at' => $comment->created_at,
            ];
        });

        $media = [
            'images' => $post->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ];
            }),
            'videos' => $post->getMedia('videos')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                ];
            }),
        ];

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
            'user' => $post->user,
        ];
    }
}
