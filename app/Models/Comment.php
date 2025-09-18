<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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
