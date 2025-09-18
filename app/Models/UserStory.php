<?php

namespace App\Models;

use App\Traits\HasViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
