<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $blocked_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $blocker
 * @property-read \App\Models\User $blockedUser
 */
class UserBlock extends Model
{
    protected $fillable = ['user_id', 'blocked_user_id'];

    /**
     * The user who initiated the block.
     */
    public function blocker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user who was blocked.
     */
    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}
