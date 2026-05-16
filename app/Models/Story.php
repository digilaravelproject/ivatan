<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property string $media_url
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story active()
 * @method static \Database\Factories\StoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereMediaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story withoutTrashed()
 * @mixin \Eloquent
 */
class Story extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'user_id',
        'media_url',
        'type',
        'expires_at',
        'is_archived',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($q)
    {
        return $q->where('expires_at', '>', Carbon::now());
    }
}
