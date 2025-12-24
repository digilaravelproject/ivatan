<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileLoginController extends Controller
{
    public function loginWithMobile(Request $request)
    {
        $request->validate([
            'mobile'        => 'required|string',
            'firebase_token'=> 'required|string',
        ]);

        try {

            /** -----------------------------------
             * 1. Verify Firebase Token
             * ----------------------------------- */
            $auth = FirebaseService::auth();
            $verifiedToken = $auth->verifyIdToken($request->firebase_token);

            $firebaseUid = $verifiedToken->claims()->get('sub');

            /** -----------------------------------
             * 2. Check User by Mobile Number
             * ----------------------------------- */
            $user = User::where('phone', $request->mobile)->first();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Mobile number not registered.',
                ], 404);
            }

            /** -----------------------------------
             * 3. Update Firebase UID / Token
             * ----------------------------------- */
            $user->update([
                'firebase_token' => $firebaseUid, // ✅ best practice
            ]);

            /** -----------------------------------
             * 3. Login User
             * ----------------------------------- */
            Auth::login($user);

            $token = $user->createToken('mobile-login')->plainTextToken;

            return response()->json([
                'status' => true,
                'message'=> 'Login successful',
                'token'  => $token,
                'user'   => $user,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP',
                'error'   => $e->getMessage(),
            ], 401);
        }
    }
}
