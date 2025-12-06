<?php

namespace App\Models;

use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'username',
        'occupation',
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'profile_photo_path',
        'status',
        'is_seller',
        'is_employer',
        'is_blocked',
        'is_verified',
        'language_preference',
        'gender',
        'date_of_birth',
        'two_factor_enabled',
        'messaging_privacy',
        'account_privacy',
        'reputation_score',
        'device_tokens',
        'email_notification_preferences',
        'followers_count',
        'following_count',
        'posts_count',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_seller' => 'boolean',
            'is_employer' => 'boolean',
            'is_blocked' => 'boolean',
            'is_verified' => 'boolean',
            'is_online' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'device_tokens' => 'array',
            'email_notification_preferences' => 'array',
            // 'interests' => 'array',
            'settings' => 'array',
            'password' => 'hashed',
        ];
    }

    /**
     * ✅ SMART ACCESSOR
     * Handles: External URLs, S3, Public Storage, and Spatie Media Sync.
     */
    public function getProfilePhotoUrlAttribute()
    {
        $path = $this->profile_photo_path;

        // 1. External URL Check (e.g. Google Login)
        if ($path && filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // 2. Database Path Check (Highest Priority)
        // Since we are now saving "49/filename.png" in the DB, this logic works perfect.
        if ($path) {
            // Detect which disk to use for generating URL
            // If AWS keys exist, generate S3 URL. If not, generate Public URL.
            $disk = (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) ? 's3' : 'public';

            // Returns: https://your-site.com/storage/49/filename.png (Public)
            // OR: https://s3.aws.../49/filename.png (S3)
            return Storage::disk($disk)->url($path);
        }

        // 3. Fallback: Spatie Media Library (If DB column is empty)
        if ($this->hasMedia('profile_photo')) {
            return $this->getFirstMediaUrl('profile_photo');
        }

        // 4. Default Avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    // --- Relationships ---


    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'interest_user', 'user_id', 'interest_id');
    }
    public function posts()
    {
        return $this->hasMany(UserPost::class);
    }
    public function reels()
    {
        return $this->hasMany(UserPost::class)->where('type', 'reel');
    }
      /* -------------------------------------------------------------------------- */
    /* Story System Relationships                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * A user has many stories.
     */
    public function stories()
    {
        return $this->hasMany(UserStory::class);
    }

    /**
     * A user has many highlights.
     */
    public function highlights()
    {
        return $this->hasMany(UserStoryHighlight::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function chatParticipants()
    {
        return $this->hasMany(UserChatParticipant::class, 'user_id');
    }

    public function chats()
    {
        return $this->belongsToMany(UserChat::class, 'user_chat_participants', 'user_id', 'chat_id')
            ->withPivot('is_admin', 'last_read_message_id')->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(UserChatMessage::class, 'sender_id');
    }
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    // --- Helpers ---
    protected ?int $cachedUnreadCount = null;
    public function getNotificationUnreadCountAttribute(): int
    {
        if ($this->cachedUnreadCount !== null) return $this->cachedUnreadCount;
        $row = DB::table('notification_unread_counts')->where('user_id', $this->id)->first();
        $this->cachedUnreadCount = $row ? (int) $row->unread_count : 0;
        return $this->cachedUnreadCount;
    }
}
