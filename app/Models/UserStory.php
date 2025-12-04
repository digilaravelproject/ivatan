<?php

namespace App\Models;

use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserStory extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasViews;

    protected $fillable = [
        'user_id',
        'type',
        'caption',
        'meta',
        'expires_at',
        'like_count'
    ];

    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'like_count' => 'integer',
    ];

    /* -------------------------------------------------------------------------- */
    /* Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_story_likes', 'story_id', 'user_id')->withTimestamps();
    }

    /* -------------------------------------------------------------------------- */
    /* Scopes                                   */
    /* -------------------------------------------------------------------------- */

    /**
     * Scope to get only active (non-expired) stories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    /* -------------------------------------------------------------------------- */
    /* Media Config                                 */
    /* -------------------------------------------------------------------------- */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('stories')
            ->useDisk('public')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();
    }
}
