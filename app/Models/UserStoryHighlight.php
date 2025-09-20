<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $cover_media_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Story> $stories
 * @property-read int|null $stories_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereCoverMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryHighlight whereUserId($value)
 * @mixin \Eloquent
 */
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
