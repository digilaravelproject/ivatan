<?php

namespace App\Models;

/**
 * App\Models\User
 *
 * @property-read \Illuminate\Support\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles)
 * @property int $id
 * @property string $uuid
 * @property string|null $username
 * @property string|null $occupation
 * @property string $name
 * @property string $email
 * @property int $is_seller
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $gender
 * @property string $language_preference
 * @property int $two_factor_enabled
 * @property string $messaging_privacy
 * @property int $is_online
 * @property string|null $last_seen_at
 * @property string|null $device_tokens
 * @property int $reputation_score
 * @property string|null $email_notification_preferences
 * @property string $account_privacy
 * @property string $password
 * @property string|null $profile_photo_path
 * @property string|null $bio
 * @property string $status
 * @property int $is_blocked
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property int $followers_count
 * @property int $following_count
 * @property string|null $settings
 * @property-read int|null $posts_count
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_employer
 * @property array<array-key, mixed>|null $interests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserChatMessage> $chatMessages
 * @property-read int|null $chat_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserChatParticipant> $chatParticipants
 * @property-read int|null $chat_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserChat> $chats
 * @property-read int|null $chats_count
 * @property-read int $notification_unread_count
 * @property-read mixed $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reel> $reels
 * @property-read int|null $reels_count
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccountPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeviceTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFollowingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsEmployer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSeller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguagePreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMessagingPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReputationScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;
    use InteractsWithMedia;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
            'interests' => 'array',
            'settings' => 'array',
            'password' => 'hashed',
        ];
    }


    public function getProfilePhotoUrlAttribute()
    {
        // First check if Spatie media exists
        if ($this->hasMedia('profile_photo')) {
            return $this->getFirstMediaUrl('profile_photo');
        }

        // Fallback to column (if you still use it)
        if ($this->profile_photo_path) {
            return Storage::disk(config('media-library.disk_name'))->url($this->profile_photo_path);
        }

        // Default avatar
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

    // Define the following relationship
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    // Define the followers relationship (inverse)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    // Define the isFollowing method to check if the user is following another user
    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
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
