<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class UserChat
 * Represents a conversation (Private or Group).
 *
 * @property int $id
 * @property string $uuid
 * @property string $type 'private' or 'group'
 * @property string|null $name
 * @property int|null $owner_id
 * @property array|null $meta
 * @property string|null $last_message_at
 */
class UserChat extends Model
{
    use HasFactory, AutoGeneratesUuid, Notifiable;

    protected $table = 'user_chats';

    protected $fillable = [
        'uuid',
        'type',
        'name',
        'owner_id',
        'meta',
        'last_message_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'last_message_at' => 'datetime',
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
