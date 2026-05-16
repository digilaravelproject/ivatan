<?php

namespace App\Services;

use App\Models\PostPreference;
use App\Models\User;
use App\Models\UserPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostPreferenceService
{
    /**
     * Set "interested" preference on a post.
     * Also records the post's author for interest-boosting logic.
     *
     * @return array{success: bool, message: string, preference: string|null}
     */
    public function markInterested(User $user, int $postId): array
    {
        return $this->setPreference($user, $postId, PostPreference::INTERESTED);
    }

    /**
     * Set "not_interested" preference on a post.
     *
     * @return array{success: bool, message: string, preference: string|null}
     */
    public function markNotInterested(User $user, int $postId): array
    {
        return $this->setPreference($user, $postId, PostPreference::NOT_INTERESTED);
    }

    /**
     * Set a preference on a post (upsert pattern: update if exists, create if not).
     *
     * @return array{success: bool, message: string, preference: string|null}
     */
    protected function setPreference(User $user, int $postId, string $preference): array
    {
        try {
            $post = UserPost::findOrFail($postId);

            // Prevent self-preference (user shouldn't mark their own posts)
            if ($post->user_id === $user->id) {
                return [
                    'success' => false,
                    'message' => 'You cannot set preferences on your own posts.',
                    'preference' => null,
                ];
            }

            DB::transaction(function () use ($user, $post, $preference) {
                PostPreference::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'post_id' => $post->id,
                    ],
                    [
                        'preference' => $preference,
                    ]
                );
            });

            $label = $preference === PostPreference::INTERESTED ? 'interested' : 'not interested';

            return [
                'success' => true,
                'message' => "Post marked as {$label}.",
                'preference' => $preference,
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Post not found.',
                'preference' => null,
            ];
        } catch (\Exception $e) {
            Log::error("Post Preference Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to set preference.',
                'preference' => null,
            ];
        }
    }

    /**
     * Remove any preference from a post.
     *
     * @return array{success: bool, message: string}
     */
    public function removePreference(User $user, int $postId): array
    {
        try {
            $deleted = PostPreference::where('user_id', $user->id)
                ->where('post_id', $postId)
                ->delete();

            if ($deleted) {
                return [
                    'success' => true,
                    'message' => 'Preference removed.',
                ];
            }

            return [
                'success' => true,
                'message' => 'No preference existed for this post.',
            ];
        } catch (\Exception $e) {
            Log::error("Remove Preference Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to remove preference.',
            ];
        }
    }

    /**
     * Get post IDs marked as "not_interested" by the user.
     * Used for feed exclusion.
     *
     * @return array<int>
     */
    public function getNotInterestedPostIds(User $user): array
    {
        return PostPreference::where('user_id', $user->id)
            ->where('preference', PostPreference::NOT_INTERESTED)
            ->pluck('post_id')
            ->all();
    }

    /**
     * Get user IDs of authors whose posts are "not_interested" by the user.
     * Used to reduce similar posts from these authors in the feed.
     *
     * @return array<int>
     */
    public function getNotInterestedAuthorIds(User $user): array
    {
        return PostPreference::where('post_preferences.user_id', $user->id)
            ->where('post_preferences.preference', PostPreference::NOT_INTERESTED)
            ->join('user_posts', 'post_preferences.post_id', '=', 'user_posts.id')
            ->pluck('user_posts.user_id')
            ->unique()
            ->all();
    }

    /**
     * Get interest category IDs from posts marked "not_interested".
     * Used to reduce similar category content in the feed.
     *
     * @return array<int>
     */
    public function getNotInterestedCategoryIds(User $user): array
    {
        // Get the author user IDs of not-interested posts
        $authorIds = $this->getNotInterestedAuthorIds($user);

        if (empty($authorIds)) {
            return [];
        }

        // Get interests of those authors to identify "similar content" categories
        return DB::table('interest_user')
            ->whereIn('user_id', $authorIds)
            ->pluck('interest_id')
            ->unique()
            ->all();
    }

    /**
     * Get post IDs marked as "interested" by the user.
     * Used for feed boosting.
     *
     * @return array<int>
     */
    public function getInterestedPostIds(User $user): array
    {
        return PostPreference::where('user_id', $user->id)
            ->where('preference', PostPreference::INTERESTED)
            ->pluck('post_id')
            ->all();
    }
}
