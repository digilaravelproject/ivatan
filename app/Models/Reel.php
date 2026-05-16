<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string|null $video_url
 * @property string|null $cover_url
 * @property string|null $description
 * @property int|null $duration_seconds
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read int|null $likes_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel active()
 * @method static \Database\Factories\ReelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereCoverUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel whereVideoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reel withoutTrashed()
 * @mixin \Eloquent
 */
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
