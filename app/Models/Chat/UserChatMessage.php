<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $chat_id
 * @property int $sender_id
 * @property string|null $content
 * @property string $message_type
 * @property string|null $attachment_path
 * @property array<array-key, mixed>|null $meta
 * @property int|null $reply_to_message_id
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat\UserChat $chat
 * @property-read UserChatMessage|null $replyTo
 * @property-read User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereReplyToMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChatMessage whereUuid($value)
 * @mixin \Eloquent
 */
class UserChatMessage extends Model
{
    use HasFactory,AutoGeneratesUuid;

    protected $table = 'user_chat_messages';

    protected $fillable = [
        'uuid', 'chat_id', 'sender_id', 'content', 'message_type', 'attachment_path', 'meta', 'reply_to_message_id', 'delivered_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'delivered_at' => 'datetime',
    ];


    public function chat()
    {
        return $this->belongsTo(UserChat::class, 'chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_message_id');
    }
}
