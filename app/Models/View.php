<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $viewable_type
 * @property int $viewable_id
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read Model|\Eloquent $viewable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereViewableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|View whereViewableType($value)
 * @mixin \Eloquent
 */
class View extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'viewable_id',
        'viewable_type',
        'ip_address',
    ];

    public function viewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
