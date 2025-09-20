<?php

namespace App\Services\Api\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        if (array_key_exists('interests', $data)) {
            $user->interests = $data['interests'] ?? ['entertainment'];
        }

        $user->save();

        return $user;
    }
}
