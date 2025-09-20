<?php

namespace App\Models;

use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $type
 * @property string|null $caption
 * @property array<array-key, mixed>|null $meta
 * @property int $like_count
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserStoryHighlight> $highlights
 * @property-read int|null $highlights_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserStoryLike> $likes
 * @property-read int|null $likes_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\View> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStory whereUserId($value)
 * @mixin \Eloquent
 */
class UserStory extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasViews;

    protected $fillable = [
        'user_id',
        'type',
        'caption',
        'meta',
        'expires_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(UserStoryLike::class);
    }

    public function highlights()
    {
        return $this->belongsToMany(UserStoryHighlight::class, 'highlight_story', 'story_id', 'highlight_id');
    }

    // Media collection for story
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('stories')->useDisk('public')->singleFile();
    }
}
