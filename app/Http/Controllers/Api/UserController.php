<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\RegisterUserRequest;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\User\UserService;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponse;

    protected $userService;
    protected NotificationService $notificationService;

    public function __construct(UserService $userService, NotificationService $notificationService)
    {
        $this->userService = $userService;
        $this->notificationService = $notificationService;
    }

    public function register(RegisterUserRequest $request)
    {
        try {
            $result = $this->userService->register($request->validated());
            $user = $result['user'];
            $user->is_own_profile = true;

            // Send welcome notification (non-blocking)
            try {
                $this->notificationService->sendToUser($user, 'welcome', [
                    'title'      => 'Welcome to Ivatan!',
                    'message'    => 'Your account has been created successfully. Start exploring!',
                    'action_url' => null,
                ]);
            } catch (\Throwable $e) {
                Log::error('Welcome notification failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }

            return $this->success([
                'user' => new UserResource($user),
                'token' => $result['token']
            ], 'User registered successfully.', 201);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $identifier = $request->identifier();

            $result = $this->userService->login($identifier, $request->password);


            if (!$result) {
                return $this->error('Invalid credentials.', 401);
            }

            $user = $result['user'];
            $user->is_own_profile = true; // Tag for resource

            return $this->success([
                'user' => new UserResource($user),
                'token' => $result['token']
            ], 'Login successful');
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    public function checkUsernameAvailability(Request $request)
    {
        try {
            $request->validate(['username' => 'required|string|max:50']);

            if (!$this->userService->isUsernameAvailable($request->username)) {
                return $this->error('Username is already taken.', 400);
            }

            return $this->success([], 'Username is available.');
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    // Add Logout
    public function logout(Request $request)
    {
        try {
            $this->userService->logout($request->user());
            return $this->success([], 'Logged out successfully.');
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    // Add Update
    public function update(UpdateUserRequest $request)
    {
        try {
            $updatedUser = $this->userService->update($request->user(), $request->validated());
            $updatedUser->is_own_profile = true;
            return $this->success(['user' => new UserResource($updatedUser)], 'Profile updated successfully.');
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error($e->getMessage());
        }
    }
    // Add Show User by Username
    public function show($username)
    {
        try {
            // Saara logic service handle karega
            $user = $this->userService->findByUsername($username);

            if (!$user) {
                return $this->error('User not found.', 404);
            }

            // Check if the authenticated user is viewing their own profile
            $user->is_own_profile = (auth('sanctum')->check() && auth('sanctum')->id() == $user->id);

            // Get extra details (following status etc)
            $user = $this->userService->attachRelationStatus($user);

            return $this->success(['user' => new UserResource($user)], 'User details retrieved successfully.');
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error($e->getMessage());
        }
    }
}
