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
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'occupation' => $data['occupation'] ?? '',
            'interests' => $data['interests'] ?? ['entertainment'],
        ]);

        $user->assignRole('user');
        if (isset($data['interests']) && is_array($data['interests'])) {
            $user->interests()->attach($data['interests']);
        }
        $token = $user->createToken('MyApp')->plainTextToken;

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
        \Log::info('Form Data:', $data);  // Check what data is being sent
        \Log::info('Has File?', ['has_file' => request()->hasFile('profile_photo')]);
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (isset($data['phone'])) {
            $user->phone = $data['phone'];
        }
        if (isset($data['username'])) {
            $user->username = $data['username'];
        }
        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        if (isset($data['date_of_birth'])) {
            $user->date_of_birth = $data['date_of_birth'];
        }
        if (array_key_exists('occupation', $data)) {
            $user->occupation = $data['occupation'] ?? '';
        }
        if (array_key_exists('bio', $data)) {
            $user->bio = $data['bio'] ?? '';
        }
        if (array_key_exists('interests', $data)) {
            $user->interests = $data['interests'] ?? ['entertainment'];
        }


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

            // Save the URL to your profile_photo_path column
            $user->profile_photo_path = $media->getUrl();
            $user->save();
            \Log::info('Profile photo updated:', [
                'user_id' => $user->id,
                'media_url' => $media->getUrl(),
                'disk' => config('media-library.disk_name'),
            ]);
        }
        \Log::info('User profile updated:', ['user_id' => $user->id, 'updated_fields' => array_keys($data)]);
        $user->save();
        $user->refresh();


        return $user;
    }
    // Username
    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }
}
