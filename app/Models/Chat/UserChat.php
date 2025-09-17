<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class UserChat extends Model
{
    use HasFactory,AutoGeneratesUuid,Notifiable;

    protected $table = 'user_chats';

    protected $fillable = [
        'uuid', 'type', 'name', 'owner_id', 'meta', 'last_message_at'
    ];

    protected $casts = [
        'meta' => 'array',
    ];



    public function participants()
    {
        return $this->hasMany(UserChatParticipant::class, 'chat_id');
    }

    public function messages()
    {
        return $this->hasMany(UserChatMessage::class, 'chat_id')->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(UserChatMessage::class, 'chat_id')->latestOfMany();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
