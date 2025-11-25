<?php

namespace App\Models;

use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    // User Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Likes Relationship (Using Pivot Table logic)
    public function likes()
    {
        return $this->belongsToMany(User::class, 'user_story_likes', 'story_id', 'user_id')->withTimestamps();
    }

    // Views Relationship
    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    // Media Configuration
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('stories')
            ->useDisk('public') // Ya fir 's3' agar configure kiya hai
            ->singleFile(); // Ek story me ek hi main file hogi
    }

    // Thumbnail Conversion for Videos/Images
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued(); // Immediate generate ho, ya queue kar sakte ho performance ke liye
    }
}
