<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
