<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
    
            $request->validate([
                'google_id' => 'required|string',
                'email'     => 'required|email',
            ]);
    
            $user = User::where('email', $request->email)
                        ->orWhere('google_id', $request->google_id)
                        ->first();
    
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No account found. Please register first.'
                ], 404);
            }
    
            // Link google_id if not already linked
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $request->google_id
                ]);
            }
    
            // OPTIONAL: revoke old tokens (recommended)
            $user->tokens()->delete();
    
            // Create token (same as screenshot)
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'data'    => $user,
                'token'   => $token
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status'  => false,
                'message' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
    
            \Log::error($e);
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
}
