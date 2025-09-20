<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $user_id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int|null $parent_id
 * @property string $body
 * @property string $status
 * @property int $like_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $commentable
 * @property-read bool $has_liked
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read Comment|null $parent
 * @property-read \App\Models\UserPost|null $post
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    protected $table = "comments";
    protected $fillable = [
        'user_id',
        'body',
        'commentable_type',
        'commentable_id',
        'parent_id'
    ];
    protected $appends = ['likes_count', 'has_liked'];

    // Relationship to the user who posted the comment
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // Polymorphic relationship for likes (can be liked by users)
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    public function commentable(): MorphTo
    {
        return $this->morphTo(); // e.g., Post, Reel, etc.
    }
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }



    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'likes', 'replies');
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }
    public function getHasLikedAttribute(): bool
    {
        if (!Auth::check()) return false;

        return $this->likes()
            ->where('user_id', Auth::id())
            ->exists();
    }
    public function post()
    {
        return $this->belongsTo(UserPost::class);
    }
}
