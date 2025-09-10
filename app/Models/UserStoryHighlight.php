<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStoryHighlight extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'cover_media_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stories()
    {
        return $this->belongsToMany(Story::class, 'highlight_story', 'highlight_id', 'story_id')->withTimestamps();
    }
}
