<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $type
 * @property string|null $name
 * @property int|null $owner_id
 * @property array<array-key, mixed>|null $meta
 * @property string|null $last_message_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat\UserChatMessage|null $lastMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat\UserChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat\UserChatParticipant> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChat whereUuid($value)
 * @mixin \Eloquent
 */
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
