<?php

namespace App\Services\Api\User;

use App\Models\User;
use App\Models\Interest;
use App\Models\Chat\UserChat;
use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\LiveChatGroupService;
use App\Services\Profile\ProfileService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * Business logic for User management (API & Admin).
 */
class UserService
{
    protected LiveChatGroupService $liveChatGroupService;
    protected ProfileService $profileService;

    public function __construct(
        LiveChatGroupService $liveChatGroupService,
        ProfileService $profileService
    ) {
        $this->liveChatGroupService = $liveChatGroupService;
        $this->profileService = $profileService;
    }

    /**
     * Helper to Detect Disk (S3 vs Public)
     */
    private function getStorageDisk()
    {
        if (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) {
            return 's3';
        }
        return 'public';
    }

    /**
     * Resolve Interest IDs from mixed input (IDs or Names)
     */
    private function resolveInterestIds(array $interests): array
    {
        $ids = array_filter($interests, 'is_numeric');
        $names = array_filter($interests, 'is_string');

        if (!empty($names)) {
            $nameIds = Interest::whereIn('name', $names)->pluck('id')->toArray();
            $ids = array_merge($ids, $nameIds);
        }

        return array_unique($ids);
    }

    /**
     * Update User Profile
     */
    public function update(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            Log::info("Starting profile update for User ID: {$user->id}");

            // 1. Update Allowed Fields
            $user->fill($data);

            // 2. Interests Update
            if (isset($data['interests']) && is_array($data['interests'])) {
                $interestIds = $this->resolveInterestIds($data['interests']);
                $user->interests()->sync($interestIds);
            }

            // 3. Profile Photo Handling
            if (isset($data['profile_photo']) && $data['profile_photo'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['profile_photo'];

                // Detect Disk automatically
                $disk = $this->getStorageDisk();
                Log::info("Uploading profile photo to disk: {$disk}");

                // Step A: Clear Old Media
                $user->clearMediaCollection('profile_photo');

                // Step B: Add New Media via Spatie
                $media = $user->addMedia($file)
                    ->usingFileName(time() . '_' . $file->getClientOriginalName())
                    ->toMediaCollection('profile_photo', $disk);

                // Step C: Update User Table Path (Optional if using Spatie's helper, but keeping for compatibility)
                $user->profile_photo_path = $media->id . '/' . $media->file_name;
            }

            $user->save();
            DB::commit();

            $user->refresh();
            $user->load('interests.category');

            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("UserService Update Failed: " . $e->getMessage());
            throw new \Exception("Failed to update profile. Please try again.");
        }
    }

    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'username' => $data['username'],
                'password' => $data['password'], // Removed Hash::make due to 'hashed' cast
                'date_of_birth' => $data['date_of_birth'],
                'occupation' => $data['occupation'] ?? '',
            ]);

            $user->assignRole('user');

            if (isset($data['interests']) && is_array($data['interests'])) {
                $interestIds = $this->resolveInterestIds($data['interests']);
                $user->interests()->attach($interestIds);
            }

            $profile = $this->profileService->createPersonalProfile($user->id);

            DB::commit();

            try {
                $this->liveChatGroupService->addUserToGroups($user);
            } catch (\Exception $e) {
                Log::warning("Failed to auto-join live groups: " . $e->getMessage());
            }

            $token = $user->createToken('MyApp')->plainTextToken;
            $user->load('interests.category');
            $user->load('profiles.activeSubscription.plan');

            return compact('user', 'token');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration Failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Login User
     */
    public function login(string $identifier, string $password): array|false
    {
        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning("Login attempt failed for identifier: {$identifier}");
            return false;
        }

        if ($user->trashed()) {
            Log::warning("Login blocked — account soft-deleted for identifier: {$identifier}");
            return false;
        }

        $user->last_login_at = now();
        $user->save();

        $token = $user->createToken('MyApp')->plainTextToken;
        $user->load('interests.category');

        return compact('user', 'token');
    }

    /**
     * Request Account Deletion
     */
    public function requestDeletion(User $user): void
    {
        DB::beginTransaction();
        try {
            $user->tokens()->delete();

            $user->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Account deletion failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Request Account Restore
     */
    public function requestRestore(string $email, string $password): User
    {
        $user = User::withTrashed()->where('email', $email)->first();

        if (!$user) {
            throw new \Exception('No account found with this email address.');
        }

        if (!Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials.');
        }

        if (!$user->trashed()) {
            throw new \Exception('Your account is already active.');
        }

        if ($user->deleted_at->lt(now()->subDays(30))) {
            throw new \Exception('The 30-day restoration period has passed. Your account has been permanently deleted.');
        }

        $user->restore();

        return $user;
    }

    public function isUsernameAvailable(string $username): bool
    {
        return !User::where('username', $username)->exists();
    }

    public function logout(User $user): void
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }
    /**
     * Attach relationship status (following/follower/chat) to a user model
     */
    public function attachRelationStatus(User $user): User
    {
        $currentUser = auth('sanctum')->user();

        $user->is_mine = false;
        $user->is_following = false;
        $user->is_follower = false;
        $user->chat_id = null;

        if ($currentUser) {
            $user->is_mine = (int)$currentUser->id === (int)$user->id;

            if (!$user->is_mine) {
                $user->is_following = $currentUser->isFollowing($user);
                $user->is_follower = $user->isFollowing($currentUser);
                $chat = UserChat::where('type', 'private')
                    ->whereHas('participants', fn($q) => $q->where('user_id', $currentUser->id))
                    ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                    ->select('id')
                    ->first();
                if ($chat) {
                    $user->chat_id = $chat->id;
                }
            }
        }

        return $user;
    }

    /**
     *  Find User by Username
     */
    public function findByUsername(string $username): ?User
    {
        return User::with('interests.category')->where('username', $username)->withCount(['followers', 'following'])->withCount(['posts' => function ($query) {
            $query->where('status', 'active');
        }])->first();
    }
}
