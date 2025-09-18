<?php

namespace App\Models;

/**
 * App\Models\User
 *
 * @property-read \Illuminate\Support\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles)
 */

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'is_seller',
        'is_employer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::disk('s3')->url($this->profile_photo_path);
        }

        // fallback default avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function reels()
    {
        return $this->hasMany(Reel::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    //
    public function chatParticipants()
    {
        return $this->hasMany(UserChatParticipant::class, 'user_id');
    }

    public function chats()
    {
        return $this->belongsToMany(
            UserChat::class,
            'user_chat_participants',
            'user_id',
            'chat_id'
        )->withPivot('is_admin', 'last_read_message_id')->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(UserChatMessage::class, 'sender_id');
    }

    // This property will hold the cached unread count per instance
    protected ?int $cachedUnreadCount = null;

    /**
     * Get the user's unread notification count (cached per request).
     *
     * @return int
     */
    public function getNotificationUnreadCountAttribute(): int
    {
        if ($this->cachedUnreadCount !== null) {
            return $this->cachedUnreadCount;
        }

        $row = DB::table('notification_unread_counts')->where('user_id', $this->id)->first();

        $this->cachedUnreadCount = $row ? (int) $row->unread_count : 0;

        return $this->cachedUnreadCount;
    }
}
