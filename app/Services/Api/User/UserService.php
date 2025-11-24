<?php

namespace App\Services\Api\User;

use App\Models\User;
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
     * Update user profile with disk-agnostic storage handling.
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function update(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            Log::info("Starting profile update for User ID: {$user->id}");

            // 1. Basic Fields Update
            $fillable = ['name', 'email', 'phone', 'username', 'date_of_birth', 'occupation', 'bio'];
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
                $user->interests()->sync($data['interests']);
            }

            // 4. Profile Photo Handling
            if (request()->hasFile('profile_photo')) {
                $file = request()->file('profile_photo');

                // Get default disk from .env (public OR s3)
                $disk = config('filesystems.default', 'public');
                Log::info("Uploading profile photo to disk: {$disk}");

                // Remove old photo if exists and is NOT a URL
                if ($user->profile_photo_path && !filter_var($user->profile_photo_path, FILTER_VALIDATE_URL)) {
                    if (Storage::disk($disk)->exists($user->profile_photo_path)) {
                        Storage::disk($disk)->delete($user->profile_photo_path);
                    }
                }

                // Generate clean filename
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

                // ✅ Store ONLY relative path (e.g., 'profile-photos/image.jpg')
                $path = $file->storeAs('profile-photos', $filename, $disk);

                $user->profile_photo_path = $path;

                // Optional: Sync Spatie Media Library
                $user->clearMediaCollection('profile_photo');
                $user->addMedia($file)->toMediaCollection('profile_photo', $disk);
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
                $user->interests()->attach($data['interests']);
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
        // ✅ Added Type Hint to fix Intelephense "Undefined method delete" error
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }

    public function findByUsername(string $username): ?User
    {
        return User::with('interests.category')->where('username', $username)->first();
    }
}
