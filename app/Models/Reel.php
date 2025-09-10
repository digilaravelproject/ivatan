<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reel extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'video_url',
        'cover_url',
        'description',
        'duration_seconds',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
