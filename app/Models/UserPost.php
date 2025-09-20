<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $type
 * @property string|null $caption
 * @property int $like_count
 * @property int $comment_count
 * @property string $status
 * @property string $visibility
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read mixed $images
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read int|null $likes_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPost withoutTrashed()
 * @mixin \Eloquent
 */
class UserPost extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;


    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'caption',
        'status',
        'visibility',
    ];

    protected $casts = [
        'like_count' => 'integer',
        'comment_count' => 'integer',
    ];
    protected $appends = ['images'];


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    //    public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'post_id');
    // }
    // In UserPost.php
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }



    // public function media()
    // {
    //     return $this->morphMany(Media::class, 'model');
    // }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }


    /*
     |--------------------------------------------------------------------------
     | Spatie Media Library
     |--------------------------------------------------------------------------
     */


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsFile(function (?File $file = null) {
                return $file && str_starts_with($file->mimeType, 'image/');
            });

        $this->addMediaCollection('videos')
            ->useDisk('public')
            ->acceptsFile(function (?File $file = null) {
                return $file && str_starts_with($file->mimeType, 'video/');
            });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && str_starts_with($media->mime_type, 'image/')) {
            $this->addMediaConversion('thumb')
                ->width(300)
                ->height(300)
                ->nonQueued();
        }

        if ($media && str_starts_with($media->mime_type, 'video/')) {
            $this->addMediaConversion('thumb')
                ->queued()
                ->extractVideoFrameAtSecond(1)
                ->performOnCollections('videos');
        }
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('images')->map(function (Media $media) {
            return [
                'id' => $media->id,
                'original_url' => $media->getUrl(),
                'thumb_url' => $media->getUrl('thumb'),
            ];
        });
    }
    /*
     |--------------------------------------------------------------------------
     | Optional: UUID Route Binding
     |--------------------------------------------------------------------------
     */

    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }
}
