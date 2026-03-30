<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property string $preference
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\UserPost $post
 */
class PostPreference extends Model
{
    const INTERESTED = 'interested';
    const NOT_INTERESTED = 'not_interested';

    protected $fillable = ['user_id', 'post_id', 'preference'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(UserPost::class, 'post_id');
    }
}
