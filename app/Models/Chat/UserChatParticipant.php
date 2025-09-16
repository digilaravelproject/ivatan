<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChatParticipant extends Model
{
    use HasFactory;

    protected $table = 'user_chat_participants';

    protected $fillable = [
        'chat_id', 'user_id', 'is_admin', 'last_read_message_id', 'joined_at'
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
