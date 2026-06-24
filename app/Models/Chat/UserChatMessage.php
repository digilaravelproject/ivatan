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

    protected static array $chatParticipantsCache = [];
    protected static array $chatsCache = [];

    public function getStatusForUserAttribute(): string
    {
        return $this->statusForUser();
    }

    public function statusForUser(?int $userId = null): string
    {
        $userId ??= request()?->user()?->id;
        if (!$userId) return 'sent';

        $cacheKey = $this->chat_id;
        if (!isset(self::$chatParticipantsCache[$cacheKey])) {
            self::$chatParticipantsCache[$cacheKey] = UserChatParticipant::where('chat_id', $this->chat_id)->get();
        }
        $participants = self::$chatParticipantsCache[$cacheKey];

        // If I am the sender of this message, check if recipient(s) have read/received it
        if ($this->sender_id === $userId) {
            $otherParticipants = $participants->filter(fn($p) => $p->user_id !== $userId);
            if ($otherParticipants->isNotEmpty()) {
                if (!isset(self::$chatsCache[$cacheKey])) {
                    self::$chatsCache[$cacheKey] = $this->chat;
                }
                $chat = self::$chatsCache[$cacheKey];
                $chatType = $chat->type ?? 'private';

                if ($chatType === 'private') {
                    $otherParticipant = $otherParticipants->first();
                    if ($otherParticipant->last_read_message_id !== null && $otherParticipant->last_read_message_id >= $this->id) {
                        return 'read';
                    }
                    if ($otherParticipant->last_delivered_message_id !== null && $otherParticipant->last_delivered_message_id >= $this->id) {
                        return 'delivered';
                    }
                } else {
                    // Group chat: check if all others have read/received
                    $totalOthers = $otherParticipants->count();
                    $readCount = $otherParticipants->filter(fn($p) => $p->last_read_message_id !== null && $p->last_read_message_id >= $this->id)->count();
                    if ($readCount === $totalOthers) {
                        return 'read';
                    }
                    $deliveredCount = $otherParticipants->filter(fn($p) => $p->last_delivered_message_id !== null && $p->last_delivered_message_id >= $this->id)->count();
                    if ($deliveredCount === $totalOthers || $readCount > 0) {
                        return 'delivered';
                    }
                }
            }
        } else {
            // If I am the receiver/recipient of this message
            $participant = $participants->firstWhere('user_id', $userId);
            if ($participant && $participant->last_read_message_id !== null && $participant->last_read_message_id >= $this->id) {
                return 'read';
            }
            if ($participant && $participant->last_delivered_message_id !== null && $participant->last_delivered_message_id >= $this->id) {
                return 'delivered';
            }
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
