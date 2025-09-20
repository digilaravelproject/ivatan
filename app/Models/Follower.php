<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $follower_id
 * @property int $following_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $follower
 * @property-read \App\Models\User $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follower whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Follower extends Model
{


    protected $fillable = ['follower_id', 'following_id'];

    protected static function booted()
    {
        static::created(function ($follower) {
            User::where('id', $follower->follower_id)->increment('following_count');
            User::where('id', $follower->following_id)->increment('followers_count');
        });

        static::deleted(function ($follower) {
            User::where('id', $follower->follower_id)->decrement('following_count');
            User::where('id', $follower->following_id)->decrement('followers_count');
        });
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
