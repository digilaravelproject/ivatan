<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'content', 'status'];

    // Relationship to the user who posted the comment
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship for likes (can be liked by users)
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // Relationship to the post this comment belongs to
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
