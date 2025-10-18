<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property int $is_admin
 * @property int|null $last_read_message_id
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat\UserChat $chat
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereLastReadMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatParticipant whereUserId($value)
 * @mixin \Eloquent
 */
class UserChatParticipant extends Model
{
    use HasFactory;

    protected $table = 'user_chat_participants';

    protected $fillable = [
        'chat_id',
        'user_id',
        'is_admin',
        'last_read_message_id',
        'joined_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(UserChat::class, 'chat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
