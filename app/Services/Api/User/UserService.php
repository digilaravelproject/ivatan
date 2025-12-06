<?php

namespace App\Services\Api\User;

use App\Models\User;
use App\Models\Interest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * Business logic for User management (API & Admin).
 */
class UserService
{
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
        $resolvedIds = [];
        foreach ($interests as $item) {
            if (is_numeric($item)) {
                $resolvedIds[] = $item;
            } elseif (is_string($item)) {
                $interest = Interest::where('name', $item)->first();
                if ($interest) {
                    $resolvedIds[] = $interest->id;
                }
            }
        }
        return array_unique($resolvedIds);
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
            // We include the new professional fields here
            $fillable = [
                // Identity
                'name',
                'email',
                'phone',
                'username',
                // Personal
                'date_of_birth',
                'occupation',
                'bio',
                'gender',
                'language_preference',
                // Privacy & Settings
                'account_privacy',
                'messaging_privacy',
                'settings',
                'email_notification_preferences'
            ];

            foreach ($fillable as $field) {
                if (array_key_exists($field, $data)) {
                    $user->$field = $data[$field];
                }
            }

            // 2. Password Update
            if (isset($data['password']) && !empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            // 3. Interests Update
            if (isset($data['interests']) && is_array($data['interests'])) {
                $interestIds = $this->resolveInterestIds($data['interests']);
                $user->interests()->sync($interestIds);
            }

            // 4. Profile Photo Handling
            if (request()->hasFile('profile_photo')) {
                $file = request()->file('profile_photo');

                // Detect Disk automatically
                $disk = $this->getStorageDisk();
                Log::info("Uploading profile photo to disk: {$disk}");

                // Step A: Clear Old Media
                $user->clearMediaCollection('profile_photo');

                // Step B: Add New Media via Spatie
                $media = $user->addMedia($file)
                    ->usingFileName(time() . '_' . $file->getClientOriginalName())
                    ->toMediaCollection('profile_photo', $disk);

                // Step C: Update User Table Path
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
                'password' => Hash::make($data['password']),
                'date_of_birth' => $data['date_of_birth'],
                'occupation' => $data['occupation'] ?? '',
            ]);

            $user->assignRole('user');

            if (isset($data['interests']) && is_array($data['interests'])) {
                $interestIds = $this->resolveInterestIds($data['interests']);
                $user->interests()->attach($interestIds);
            }

            $token = $user->createToken('MyApp')->plainTextToken;
            $user->load('interests.category');

            DB::commit();
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
            return false;
        }

        $user->last_login_at = now();
        $user->save();

        $token = $user->createToken('MyApp')->plainTextToken;
        $user->load('interests.category');

        return compact('user', 'token');
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
     * Get full user profile with relation status (following/follower)
     */
    public function getUserProfileDetails(string $username): ?array
    {
        // 1. User Fetch karein
        $user = $this->findByUsername($username);

        if (!$user) {
            return null;
        }

        // 2. Viewer (Logged-in user) ko identify karein
        // Hum service ke andar hi auth check kar rahe hain taaki controller clean rahe
        $currentUser = auth('sanctum')->user();

        $isMine = false;
        $isFollowing = false;
        $isFollower = false;

        if ($currentUser) {
            // Check: Kya ye profile meri hai?
            $isMine = (int)$currentUser->id === (int)$user->id;

            if (!$isMine) {
                // Check relations only if it's not my own profile
                $isFollowing = $currentUser->isFollowing($user);
                $isFollower = $user->isFollowing($currentUser);
            }
        }

        // 3. Extra Counts (Active Posts)
        $postCount = $user->posts()->where('status', 'active')->count();

        // 4. Data Merge & Return
        $userData = $user->toArray();

        // Custom fields inject kar rahe hain
        $userData['posts_count'] = $postCount;
        $userData['is_mine'] = $isMine;
        $userData['is_following'] = $isFollowing;
        $userData['is_follower'] = $isFollower;

        return $userData;
    }
    /**
     *  Find User by Username
     */
    public function findByUsername(string $username): ?User
    {
        return User::with('interests.category')->where('username', $username)->first();
    }
}
