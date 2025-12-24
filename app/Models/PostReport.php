<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'reason',
        'description',
    ];

    public function post()
    {
        return $this->belongsTo(UserPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
