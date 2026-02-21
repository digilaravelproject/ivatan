<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminPostController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'post');

        $posts = UserPost::with(['user', 'likes.user', 'media'])
            ->withCount(['likes', 'comments'])
            ->where('type', $type)
            ->active()
            ->latest()
            ->paginate(20);

        $posts->getCollection()->transform(function ($post) {
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
        $post = UserPost::with(['user', 'likes.user', 'media'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);

        // Load all comments for this post with user in one query
        $comments = Comment::with('user')
            ->where('id', $post->id)
            ->orderBy('created_at')
            ->get();

        // Build comment tree with nested replies
        $commentTree = $this->buildCommentTree($comments);

        // Format comments for view
        $formattedComments = $this->formatCommentTree($commentTree);

        $postDetails = $this->preparePostDetails($post, $formattedComments);

        $statuses = ['active', 'deleted', 'flagged'];
        $visibilities = ['public', 'private'];

        return view('admin.post.details', compact('postDetails', 'statuses', 'visibilities'));
    }

    // Recursive function to build tree of comments based on parent_id
    private function buildCommentTree(Collection $comments, $parentId = null)
    {
        return $comments->where('parent_id', $parentId)->map(function ($comment) use ($comments) {
            $comment->replies = $this->buildCommentTree($comments, $comment->id);
            return $comment;
        });
    }

    // Format comment tree to array structure like before
    private function formatCommentTree(Collection $commentTree)
    {
        return $commentTree->map(function ($comment) {
            return [
                'id' => $comment->id,
                'comment_id' => $comment->id,
                'user_id' => $comment->user_id,
                'username' => $comment->user->name,
                'profile_picture' => $comment->user->profile_photo_path ?? asset('images/default-avatar.png'),
                'content' => $comment->body,
                'created_at' => $comment->created_at,
                'like_count' => $comment->likes()->count(),
                'replies' => $this->formatCommentTree($comment->replies),
            ];
        });
    }

    private function preparePostDetails(UserPost $post, $formattedComments = null)
    {
        $totalLikes = $post->likes()->count();

        $likes = $post->likes->map(fn($like) => $this->formatLike($like));

        // Use formatted comments from the built tree or fallback
        $comments = $formattedComments ?? $post->comments->map(fn($comment) => $this->formatComment($comment));

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
            'total_comments' => count($comments),
            'likes' => $likes,
            'comments' => $comments,
            'user' => $post->user,
            'profile_pic' => $post->user->profile_photo_url,
        ];
    }

    private function formatLike($like)
    {
        return [
            'id' => $like->id,
            'user_id' => $like->user_id,
            'username' => $like->user->name,
            'profile_picture' => $like->user->profile_photo_path ?? asset('images/default-avatar.png'),
        ];
    }

    // This function is now only used for non-nested comments fallback
    private function formatComment($comment)
    {
        return [
            'id' => $comment->id,
            'comment_id' => $comment->id,
            'user_id' => $comment->user_id,
            'username' => $comment->user->name,
            'profile_picture' => $comment->user->profile_photo_path ?? asset('images/default-avatar.png'),
            'content' => $comment->body,
            'created_at' => $comment->created_at,
            'like_count' => $comment->likes()->count(),
            'replies' => [], // replies handled in comment tree
        ];
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,deleted,flagged',
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
            \Log::error('Post update failed', [
                'post_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update post.');
        }
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::with('replies')->findOrFail($commentId);

        // Use recursive delete for replies but avoid N+1 by eager loading
        $this->deleteReplies($comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }

    private function deleteReplies(Comment $comment)
    {
        // Eager load all replies in one go to avoid N+1
        $replies = Comment::with('replies')->where('parent_id', $comment->id)->get();

        foreach ($replies as $reply) {
            $this->deleteReplies($reply);
            $reply->delete();
        }
    }

    public function getLikes($id)
    {
        $post = UserPost::with('likes.user')->findOrFail($id);

        $likes = $post->likes->map(fn($like) => $this->formatLike($like));

        return view('admin.post.likes', compact('likes', 'post'));
    }

    // Paginate comments for scalability
    public function getComments(Request $request, $id)
    {
        $post = UserPost::findOrFail($id);

        $comments = Comment::with('user')
            ->where('id', $post->id)
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->paginate(20);

        // For each comment, eager load replies and format
        $comments->getCollection()->load('replies.user');

        $formattedComments = $comments->getCollection()->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'username' => $comment->user->name,
                'profile_picture' => $comment->user->profile_photo_path ?? asset('images/default-avatar.png'),
                'content' => $comment->body,
                'created_at' => $comment->created_at,
                'like_count' => $comment->likes()->count(),
                'replies' => $comment->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'user_id' => $reply->user_id,
                        'username' => $reply->user->name,
                        'profile_picture' => $reply->user->profile_photo_path ?? asset('images/default-avatar.png'),
                        'content' => $reply->body,
                        'created_at' => $reply->created_at,
                        'like_count' => $reply->likes()->count(),
                    ];
                }),
            ];
        });

        return view('admin.post.comments', [
            'comments' => $formattedComments,
            'post' => $post,
            'pagination' => $comments, // To show pagination links if needed
        ]);
    }

    public function softDelete($id)
    {
        $post = UserPost::findOrFail($id);

        $post->delete();

        return redirect()->route('admin.userpost.index')->with('success', 'Post soft deleted successfully.');
    }

    public function forceDelete($id)
    {
        $post = UserPost::withTrashed()->findOrFail($id);

        $post->comments()->delete();

        $post->likes()->delete();

        $post->clearMediaCollection('images');
        $post->clearMediaCollection('videos');

        $post->forceDelete();

        return redirect()->route('admin.userpost.index')->with('success', 'Post permanently deleted.');
    }
}
