<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
