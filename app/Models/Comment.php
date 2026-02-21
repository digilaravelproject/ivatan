<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    protected $table = "comments";

    protected $fillable = [
        'user_id',
        'body',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'status'
    ];

    // Note: We removed 'appends' here because we are doing the calculation
    // inside the API Resource. This makes the database queries much faster
    // when you have long lists of comments.

    /* -----------------------------------------------------------------
     | Relationships
     | -----------------------------------------------------------------
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /* -----------------------------------------------------------------
     | Accessors (Helpers for the Resource)
     | -----------------------------------------------------------------
     */

    public function getLikesCountAttribute(): int
    {
        // If loaded via withCount('likes'), use that. Otherwise query it.
        if (array_key_exists('likes_count', $this->attributes)) {
            return $this->attributes['likes_count'];
        }
        return $this->likes()->count();
    }

    public function getHasLikedAttribute(): bool
    {
        if (!Auth::check()) return false;

        // Check loaded relation first to avoid N+1 queries
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', Auth::id());
        }

        return $this->likes()
            ->where('user_id', Auth::id())
            ->exists();
    }
}
