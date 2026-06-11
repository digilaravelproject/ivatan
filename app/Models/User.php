<?php

namespace App\Models;

use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\Ad;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use App\Models\Jobs\UserJobPost;
use App\Models\UserPost;
use App\Models\UserStory;
use App\Models\UserStoryHighlight;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


/**
 * @property Collection<int, Interest> $interests
 */

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'google_id',
        'firebase_token',
        'username',
        'occupation',
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'profile_photo_path',
        'status',
        'is_admin',
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
        'hide_email',
        'hide_phone',
        'is_online',
        'last_seen_at',
        'is_busy',
        'busy_status',
        'gateway_customer_id',
        'deletion_reason',
    ];

    protected $appends = ['profile_photo_url'];

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
            'is_admin' => 'boolean',
            'is_seller' => 'boolean',
            'is_employer' => 'boolean',
            'is_blocked' => 'boolean',
            'is_verified' => 'boolean',
            'is_online' => 'boolean',
            'is_busy' => 'boolean',
            'busy_status' => 'string',
            'two_factor_enabled' => 'boolean',
            'device_tokens' => 'array',
            'email_notification_preferences' => 'array',
            // 'interests' => 'array',
            'settings' => 'array',
            'hide_email' => 'boolean',
            'hide_phone' => 'boolean',
            'deletion_reason' => 'array',
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

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
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

    public function isFollowing(?User $user): bool
    {
        if (!$user) return false;
        return $this->following()->where('following_id', $user->id)->exists();
    }

    // --- Bookmark, Block, Preference Relationships ---

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Users that I have blocked.
     */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_user_id')
            ->withTimestamps();
    }

    /**
     * Users that have blocked me.
     */
    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_user_id', 'user_id')
            ->withTimestamps();
    }

    public function postPreferences()
    {
        return $this->hasMany(PostPreference::class);
    }

    // --- Block Helpers (Per-Request Cached) ---

    protected ?array $cachedBlockedIds = null;
    protected ?array $cachedBlockedByIds = null;

    /**
     * Get IDs of users I have blocked (cached per request lifecycle).
     */
    public function getBlockedUserIds(): array
    {
        if ($this->cachedBlockedIds === null) {
            $this->cachedBlockedIds = UserBlock::where('user_id', $this->id)
                ->pluck('blocked_user_id')
                ->all();
        }
        return $this->cachedBlockedIds;
    }

    /**
     * Get IDs of users who have blocked me (cached per request lifecycle).
     */
    public function getBlockedByUserIds(): array
    {
        if ($this->cachedBlockedByIds === null) {
            $this->cachedBlockedByIds = UserBlock::where('blocked_user_id', $this->id)
                ->pluck('user_id')
                ->all();
        }
        return $this->cachedBlockedByIds;
    }

    /**
     * Get ALL user IDs to exclude from feed (users I blocked + users who blocked me).
     */
    public function getAllBlockedIds(): array
    {
        return array_unique(array_merge(
            $this->getBlockedUserIds(),
            $this->getBlockedByUserIds()
        ));
    }

    /**
     * Check if I have blocked a specific user.
     */
    public function hasBlocked(User $user): bool
    {
        return in_array($user->id, $this->getBlockedUserIds());
    }

    /**
     * Check if a specific user has blocked me.
     */
    public function isBlockedBy(User $user): bool
    {
        return in_array($user->id, $this->getBlockedByUserIds());
    }

    /**
     * Check if ANY block exists between me and another user (bidirectional).
     */
    public function hasBlockRelationWith(User $user): bool
    {
        return $this->hasBlocked($user) || $this->isBlockedBy($user);
    }

    /**
     * Clear the per-request block cache (call after block/unblock action).
     */
    public function clearBlockCache(): void
    {
        $this->cachedBlockedIds = null;
        $this->cachedBlockedByIds = null;
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });

        static::deleting(function (User $user) {
            if ($user->isForceDeleting()) {
                return;
            }

            // Cascade soft-delete to user's content
            $user->posts()->each(fn($p) => $p->delete());
            $user->stories()->each(fn($s) => $s->delete());
            $user->highlights()->each(fn($h) => $h->delete());
            $user->ads()->each(fn($a) => $a->delete());
            $user->deviceTokens()->each(fn($t) => $t->delete());

            // Ecommerce items
            UserProduct::where('seller_id', $user->id)->each(fn($p) => $p->delete());
            UserService::where('seller_id', $user->id)->each(fn($s) => $s->delete());
            UserJobPost::where('employer_id', $user->id)->update(['status' => 'inactive']);

            // Profiles & subscriptions
            $user->profiles->each(fn(Profile $profile) => $profile->delete());
            $user->subscriptions()->whereIn('status', ['active', 'past_due', 'pending'])
                ->update(['status' => 'expired']);
        });

        static::restoring(function (User $user) {
            // Cascade restore to user's content
            UserPost::withTrashed()->where('user_id', $user->id)->whereNotNull('deleted_at')->each(fn($p) => $p->restore());
            UserStory::withTrashed()->where('user_id', $user->id)->whereNotNull('deleted_at')->each(fn($s) => $s->restore());
            UserStoryHighlight::withTrashed()->where('user_id', $user->id)->whereNotNull('deleted_at')->each(fn($h) => $h->restore());
            Ad::withTrashed()->where('user_id', $user->id)->whereNotNull('deleted_at')->each(fn($a) => $a->restore());

            // Ecommerce items
            UserProduct::withTrashed()->where('seller_id', $user->id)->whereNotNull('deleted_at')->each(fn($p) => $p->restore());
            UserService::withTrashed()->where('seller_id', $user->id)->whereNotNull('deleted_at')->each(fn($s) => $s->restore());
            UserJobPost::where('employer_id', $user->id)->where('status', 'inactive')->update(['status' => 'active']);

            // Profiles
            Profile::withTrashed()->where('user_id', $user->id)->whereNotNull('deleted_at')->each(fn($p) => $p->restore());

            // Note: Subscriptions remain expired (billing logic)
            // Note: DeviceTokens not restored (re-created on login)
        });
    }

    // --- Profile & Subscription Relationships ---

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function activeProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('is_active', true)->where('status', 'active');
    }

    public function defaultProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('is_default', true);
    }

    public function profileSwitchRequests(): HasMany
    {
        return $this->hasMany(ProfileSwitchRequest::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class)
            ->whereIn('status', ['active', 'past_due'])
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>', now());
            });
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

    public function getIsEmployerAttribute($value): bool
    {
        if ($value) {
            return true;
        }
        return $this->activeProfile()->where('type', 'employer')->exists();
    }

    public function getIsSellerAttribute($value): bool
    {
        if ($value) {
            return true;
        }
        return $this->activeProfile()->where('type', 'seller')->exists();
    }
}
