<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'caption',
        'type',
        'media_metadata',
        'status',
        'visibility',
    ];

    protected $casts = [
        'media_metadata' => 'array', // media_metadata will be an array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    // Add a method to store image URLs in media_metadata
    public function setMediaMetadata(array $media)
    {
        $this->media_metadata = $media;
        $this->save();
    }


    // Add a method to simulate comments (just for demonstration purposes)
    // public function addComment($comment)
    // {
    //     $media = $this->media_metadata;
    //     if (!isset($media['comments'])) {
    //         $media['comments'] = [];
    //     }
    //     $media['comments'][] = $comment;
    //     $this->setMediaMetadata($media);
    // }

    // Get the likes relationship (for polymorphic relation)
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // Get the comments relationship
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
