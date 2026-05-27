<?php

namespace App\Services;

use App\Enums\PostType;
use App\Models\User;
use App\Models\UserPost;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostFeedService
{
    public function __construct(
        private PostPreferenceService $preferenceService,
    ) {}

    private function authUser(): ?User
    {
        /** @var User|null $user */
        return Auth::guard('sanctum')->user();
    }

    private function applyBaseQueryOptimizations($query, ?User $user = null)
    {
        $userId = $user?->id;

        if ($user) {
            $blockedIds = $user->getAllBlockedIds();
            if (!empty($blockedIds)) {
                $query->whereNotIn('user_posts.user_id', $blockedIds);
            }

            $notInterestedPostIds = $this->preferenceService->getNotInterestedPostIds($user);
            if (!empty($notInterestedPostIds)) {
                $query->whereNotIn('user_posts.id', $notInterestedPostIds);
            }

            $notInterestedAuthorIds = $this->preferenceService->getNotInterestedAuthorIds($user);
            if (!empty($notInterestedAuthorIds)) {
                $idList = implode(',', array_map('intval', $notInterestedAuthorIds));
                $query->orderByRaw("CASE WHEN user_posts.user_id IN ({$idList}) THEN 1 ELSE 0 END ASC");
            }
        }

        $baseQuery = $query->withExists('likes');

        if ($userId) {
            $baseQuery->withExists([
                'bookmarks as bookmarks_exists' => function ($bq) use ($userId) {
                    $bq->where('user_id', $userId);
                }
            ]);
        }

        return $baseQuery->with([
            'media',
            'user' => function ($q) use ($userId) {
                $q->with(['interests', 'media']);
                if ($userId) {
                    $q->withExists([
                        'followers as is_followed_by_me' => function ($f) use ($userId) {
                            $f->where('follower_id', $userId);
                        }
                    ]);
                }
            }
        ]);
    }

    public function mixedFeed(): Paginator
    {
        return $this->applyBaseQueryOptimizations(UserPost::query(), $this->authUser())
            ->forYou()
            ->simplePaginate(15);
    }

    public function postsFeed(): Paginator
    {
        return $this->applyBaseQueryOptimizations(
            UserPost::query()->whereIn('type', PostType::imageFeedTypes()),
            $this->authUser()
        )
            ->trending()
            ->simplePaginate(15);
    }

    public function videosFeed(): Paginator
    {
        return $this->applyBaseQueryOptimizations(
            UserPost::query()->ofType(PostType::Video->value),
            $this->authUser()
        )
            ->trending()
            ->simplePaginate(15);
    }

    public function reelsFeed(): Paginator
    {
        return $this->applyBaseQueryOptimizations(
            UserPost::query()->ofType(PostType::Reel->value),
            $this->authUser()
        )
            ->trending()
            ->simplePaginate(15);
    }

    public function getRelatedVideos(string $id): Collection
    {
        $post = UserPost::findOrFail($id);
        $userId = $post->user_id;

        $interestIds = DB::table('interest_user')
            ->where('user_id', $userId)
            ->pluck('interest_id');

        $query = UserPost::query()
            ->whereIn('type', PostType::videoTypes())
            ->where('id', '!=', $post->id);

        $query->where(function ($q) use ($userId, $interestIds) {
            $q->where('user_id', $userId);
            if ($interestIds->isNotEmpty()) {
                $q->orWhereExists(function ($sub) use ($interestIds) {
                    $sub->select(DB::raw(1))
                        ->from('interest_user')
                        ->whereColumn('interest_user.user_id', 'user_posts.user_id')
                        ->whereIn('interest_user.interest_id', $interestIds);
                });
            }
        });

        $relatedPosts = $this->applyBaseQueryOptimizations($query, $this->authUser())
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($relatedPosts->count() < 5) {
            $trendingQuery = UserPost::query()
                ->whereIn('type', PostType::videoTypes())
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'));

            $trendingPosts = $this->applyBaseQueryOptimizations($trendingQuery, $this->authUser())
                ->trending()
                ->limit(10 - $relatedPosts->count())
                ->get();

            $relatedPosts = $relatedPosts->merge($trendingPosts);
        }

        return $relatedPosts;
    }

    public function getUserPosts(int $userId, string $filter): Paginator
    {
        $query = UserPost::query()->where('user_id', $userId);

        $query = $this->applyBaseQueryOptimizations($query, $this->authUser());

        if ($filter === 'posts') {
            $query->whereIn('type', PostType::userPostFilterTypes());
        } elseif ($filter === 'videos') {
            $query->whereIn('type', PostType::userVideoFilterTypes());
        }

        return $query->orderBy('created_at', 'DESC')
            ->simplePaginate(12);
    }

    public function globalTrendingFeed(mixed $seed): Paginator
    {
        return $this->applyBaseQueryOptimizations(UserPost::query(), $this->authUser())
            ->orderByRaw("RAND($seed)")
            ->simplePaginate(15);
    }

    public function trendingInterestsFeed(Request $request): Paginator
    {
        $seed = $request->input('seed') ?: time();
        $user = $this->authUser();

        if (!$user) {
            return $this->globalTrendingFeed($seed);
        }

        $interestIds = $user->interests()->pluck('interests.id');

        if ($interestIds->isEmpty()) {
            return $this->globalTrendingFeed($seed);
        }

        $relatedUserIds = DB::table('interest_user')
            ->whereIn('interest_id', $interestIds)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->unique();

        if ($relatedUserIds->isEmpty()) {
            return $this->globalTrendingFeed($seed);
        }

        $posts = $this->applyBaseQueryOptimizations(
            UserPost::query()->whereIn('user_id', $relatedUserIds),
            $user
        )
            ->orderByRaw("RAND($seed)")
            ->simplePaginate(15);

        if ($posts->isEmpty() && (int) $request->input('page', 1) === 1) {
            return $this->globalTrendingFeed($seed);
        }

        return $posts;
    }

    public function forYouFeed(Request $request): Paginator
    {
        $seed = $request->input('seed') ?: time();
        $user = $this->authUser();

        $query = UserPost::query();

        if ($user) {
            $interestIds = $user->interests()->pluck('interests.id');
            if ($interestIds->isNotEmpty()) {
                $relatedUserIds = DB::table('interest_user')
                    ->whereIn('interest_id', $interestIds)
                    ->pluck('user_id')
                    ->unique();

                if ($relatedUserIds->isNotEmpty()) {
                    $query->whereIn('user_id', $relatedUserIds);
                }
            }
        }

        $posts = $this->applyBaseQueryOptimizations($query, $user)
            ->orderByRaw("RAND($seed)")
            ->simplePaginate(15);

        if ($posts->isEmpty() && (int) $request->input('page', 1) === 1) {
            $posts = $this->applyBaseQueryOptimizations(UserPost::query(), $user)
                ->orderByRaw("RAND($seed)")
                ->simplePaginate(15);
        }

        return $posts;
    }
}
