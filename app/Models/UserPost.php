<?php

namespace App\Models;

use App\Traits\VisibilityTrait;
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
 * @property int $view_count
 * @property string $status
 * @property string $visibility
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $images
 */
class UserPost extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    use VisibilityTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'type',         // e.g., 'post', 'reel', 'video'
        'caption',
        'like_count',
        'comment_count',
        'view_count',   // Code 1 se liya (Important)
        'status',       // 'active', 'inactive'
        'visibility',   // 'public', 'private'
    ];

    // ✅ FIX: View count ko integer banana zaroori hai calculation ke liye
    protected $casts = [
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'view_count' => 'integer',
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

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Algorithms)
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

    /**
     * Trending Algorithm:
     * Formula: (Views * 1) + (Likes * 5) + (Comments * 10)
     */
    public function scopeTrending($query)
    {
        return $query->active()
            ->orderByRaw('(view_count + (like_count * 5) + (comment_count * 10)) DESC')
            ->orderBy('created_at', 'DESC');
    }

    /**
     * For You Algorithm:
     * Mix of Freshness (30 days) + Engagement Score + Randomness
     */
    public function scopeForYou($query)
    {
        return $query->active()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByRaw('(view_count + (like_count * 5) + (comment_count * 10)) DESC')
            ->inRandomOrder();
    }

    /*
    |--------------------------------------------------------------------------
    | Spatie Media Library (Optimized)
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        // 1. Images Collection - Only accepts images
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsFile(function (File $file) {
                return str_starts_with($file->mimeType, 'image/');
            });

        // 2. Videos Collection - Only accepts videos (Prevents crashes)
        $this->addMediaCollection('videos')
            ->useDisk('public')
            ->acceptsFile(function (File $file) {
                return str_starts_with($file->mimeType, 'video/');
            });
    }

    /**
     * ✅ THUMBNAIL GENERATION LOGIC
     * Using Code 1 logic because it works better for Reels/Videos
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // 1. Standard Image Thumbnail (300x300)
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued(); // Images ke liye instant conversion better hai

        // 2. Video/Reel Poster (Using Code 1 Logic)
        // Ye logic specific 'videos' collection pe apply hoga
        $this->addMediaConversion('thumb')
            ->width(480)   // Reel Aspect Ratio width
            ->height(854)  // Reel Aspect Ratio height
            ->extractVideoFrameAtSecond(1) // 1st second ka frame lega
            ->performOnCollections('videos')
            ->queued();    // Videos heavy hote hain, isliye queue me dala
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImagesAttribute()
    {
        return $this->getMedia('images')->map(function (Media $media) {
            return [
                'id' => $media->id,
                'original_url' => $media->getUrl(),
                'thumb_url' => $media->getUrl('thumb'),
                'mime_type' => $media->mime_type, // Added mime_type for frontend check
            ];
        });
    }
}
