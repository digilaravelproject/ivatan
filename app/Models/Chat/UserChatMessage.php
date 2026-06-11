<?php

namespace App\Models\Chat;

use App\Models\Chat\UserChatParticipant;
use App\Models\User;
use App\Traits\AutoGeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserChatMessage extends Model
{
    use HasFactory, AutoGeneratesUuid, SoftDeletes; // Soft Deletes for "Everyone"

    protected $table = 'user_chat_messages';

    protected $fillable = [
        'uuid',
        'chat_id',
        'sender_id',
        'content',
        'message_type', // text, image, file, system
        'attachment_path',
        'meta',
        'reply_to_message_id',
        'delivered_at',
        'hidden_for_users' // New JSON Column
    ];

    protected $casts = [
        'meta' => 'array',
        'hidden_for_users' => 'array',
        'delivered_at' => 'datetime',
    ];

    // --- Accessor ---
    public function getStatusForUserAttribute()
    {
        $userId = request()->user()?->id;
        if (!$userId) return 'sent';

        $participant = UserChatParticipant::where('chat_id', $this->chat_id)
            ->where('user_id', $userId)
            ->first();

        if ($participant && $participant->last_read_message_id !== null && $participant->last_read_message_id >= $this->id) {
            return 'read';
        }

        if ($this->delivered_at) {
            return 'delivered';
        }

        return 'sent';
    }

    // --- Relations ---

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
        return $this->belongsTo(self::class, 'reply_to_message_id')->withTrashed();
    }

    // --- Scopes ---

    // Filter out messages deleted "For Me"
    public function scopeVisibleToUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('hidden_for_users')
                ->orWhereJsonDoesntContain('hidden_for_users', $userId);
        });
    }
}
