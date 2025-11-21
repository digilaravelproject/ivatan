<?php

namespace App\Services\Api\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class UserService
{
    public function register(array $data): array
    {
        // 1. Create Basic User
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'occupation' => $data['occupation'] ?? '',
            // 'interests' column agar DB me nahi hai to yaha se hata sakte ho,
            // pivot table niche handle ho rahi hai.
        ]);

        $user->assignRole('user');

        // 2. Attach Interests (Many-to-Many)
        if (isset($data['interests']) && is_array($data['interests'])) {
            $user->interests()->attach($data['interests']);
        }

        $token = $user->createToken('MyApp')->plainTextToken;

        // ✅ LOAD CATEGORY: Response me category name bhejne ke liye
        $user->load('interests.category');

        return compact('user', 'token');
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

        // ✅ LOAD CATEGORY: Login ke time bhi full details bhejo
        $user->load('interests.category');

        return compact('user', 'token');
    }

    public function isUsernameAvailable(string $username): bool
    {
        return !User::where('username', $username)->exists();
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function update(User $user, array $data): User
    {
        \Log::info('Form Data:', $data);
        \Log::info('Has File?', ['has_file' => request()->hasFile('profile_photo')]);

        // Update basic fields
        if (isset($data['name'])) $user->name = $data['name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (isset($data['phone'])) $user->phone = $data['phone'];
        if (isset($data['username'])) $user->username = $data['username'];
        if (isset($data['password'])) $user->password = Hash::make($data['password']);
        if (isset($data['date_of_birth'])) $user->date_of_birth = $data['date_of_birth'];
        if (array_key_exists('occupation', $data)) $user->occupation = $data['occupation'] ?? '';
        if (array_key_exists('bio', $data)) $user->bio = $data['bio'] ?? '';

        // ✅ UPDATE INTERESTS: Use sync() for pivot table
        if (isset($data['interests']) && is_array($data['interests'])) {
            // sync purane interests hata kar naye daal dega, jo update ke liye best hai
            $user->interests()->sync($data['interests']);
        }

        // Profile Photo Logic
        if (request()->hasFile('profile_photo')) {
            \Log::info('Request has file?', [
                'hasFile' => request()->hasFile('profile_photo'),
                'file' => request()->file('profile_photo'),
            ]);

            // Delete old photo
            $user->clearMediaCollection('profile_photo');
            \Log::info('Clearing old media for user: ' . $user->id);

            // Upload new one
            $media = $user
                ->addMediaFromRequest('profile_photo')
                ->usingFileName(time() . '_' . request()->file('profile_photo')->getClientOriginalName())
                ->toMediaCollection('profile_photo', config('media-library.disk_name'));

            // Save the URL
            $user->profile_photo_path = $media->getUrl();

            \Log::info('Profile photo updated:', [
                'user_id' => $user->id,
                'media_url' => $media->getUrl(),
                'disk' => config('media-library.disk_name'),
            ]);
        }

        $user->save();

        // ✅ REFRESH & LOAD: Naya data aur relationships wapas bhejo
        $user->refresh();
        $user->load('interests.category');

        \Log::info('User profile updated:', ['user_id' => $user->id]);

        return $user;
    }

    // Username Search
    public function findByUsername(string $username): ?User
    {
        // ✅ EAGER LOAD: Public profile view par bhi categories dikhengi
        return User::with('interests.category')
            ->where('username', $username)
            ->first();
    }
}
