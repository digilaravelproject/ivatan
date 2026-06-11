<?php

namespace App\Models;

use App\Models\Chat\UserChat;
use Illuminate\Database\Eloquent\Model;

class UserCallSession extends Model
{
    protected $table = 'user_call_sessions';

    protected $fillable = [
        'uuid',
        'chat_id',
        'caller_id',
        'receiver_id',
        'type',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(UserChat::class, 'chat_id');
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
