<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\User;
use App\Models\UserPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookmarkService
{
    /**
     * Toggle bookmark on a post (Idempotent toggle pattern).
     *
     * @return array{success: bool, message: string, is_bookmarked: bool}
     */
    public function toggle(User $user, int $postId): array
    {
        try {
            $post = UserPost::findOrFail($postId);

            return DB::transaction(function () use ($user, $post) {
                $existing = Bookmark::where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->first();

                if ($existing) {
                    $existing->delete();
                    return [
                        'success' => true,
                        'message' => 'Post removed from bookmarks.',
                        'is_bookmarked' => false,
                    ];
                }

                Bookmark::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Post added to bookmarks.',
                    'is_bookmarked' => true,
                ];
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Post not found.',
                'is_bookmarked' => false,
            ];
        } catch (\Exception $e) {
            Log::error("Bookmark Toggle Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to toggle bookmark.',
                'is_bookmarked' => false,
            ];
        }
    }

    /**
     * Get user's bookmark collection (paginated).
     * Optimized with eager loading to prevent N+1.
     *
     * @param User $user
     * @param string|null $type Filter by post type (post, video, reel, carousel)
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCollection(User $user, ?string $type = null, int $perPage = 15)
    {
        $query = Bookmark::where('bookmarks.user_id', $user->id)
            ->join('user_posts', 'bookmarks.post_id', '=', 'user_posts.id')
            ->whereNull('user_posts.deleted_at') // Respect soft deletes
            ->where('user_posts.status', 'active');

        // Filter by post type
        if ($type && in_array($type, ['post', 'video', 'reel', 'carousel'])) {
            $query->where('user_posts.type', $type);
        }

        // Select bookmark fields + ordering
        $query->select('bookmarks.*')
            ->orderBy('bookmarks.created_at', 'DESC');

        return $query->with([
            'post' => function ($q) {
                $q->with([
                    'media',
                    'user' => function ($uq) {
                        $uq->with(['interests', 'media']);
                    }
                ]);
            }
        ])->paginate($perPage);
    }
}
