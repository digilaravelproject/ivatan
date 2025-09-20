<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $story_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Story $story
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereStoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStoryLike whereUserId($value)
 * @mixin \Eloquent
 */
class UserStoryLike extends Model
{
    use HasFactory;

    protected $fillable = ['story_id', 'user_id'];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
