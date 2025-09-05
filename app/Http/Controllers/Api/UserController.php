<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // User Registration API
public function register(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Generate UUID
        $uuid = Str::uuid();
        \Log::info('Generated UUID: ' . $uuid); // Log UUID to check it

        // Create the user
        $user = User::create([
            'uuid' => $uuid,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the default "user" role using Spatie's assignRole method
        $user->assignRole('user');

        // Create the token for the user
        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    // User Login API
    public function login(Request $request)
    {
         try {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('MyApp')->plainTextToken;
        // $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
         } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }

}
