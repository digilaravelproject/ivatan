<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use App\Traits\Blockable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory, AutoGeneratesUuid, Blockable;

    protected $table = 'user_chats';

    protected $fillable = [
        'uuid',
        'type', // private, group
        'name',
        'avatar_path',
        'owner_id',
        'meta',
        'last_message_at',
        'live_chat_group_id',
        'chat_mode',
    ];

    protected $casts = [
        'meta' => 'array',
        'last_message_at' => 'datetime',
    ];

    // --- Accessor ---
    public function getAvatarUrlAttribute()
    {
        return $this->avatar_path ? url('storage/' . $this->avatar_path) : null;
    }

    // --- Relations ---

    public function participants()
    {
        return $this->hasMany(UserChatParticipant::class, 'chat_id');
    }

    public function messages()
    {
        return $this->hasMany(UserChatMessage::class, 'chat_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(UserChatMessage::class, 'chat_id')->latestOfMany();
    }

    public function liveChatGroup()
    {
        return $this->belongsTo(\App\Models\LiveChatGroup::class, 'live_chat_group_id');
    }
}
