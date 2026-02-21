<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * STEP 1: Verify OTP (Firebase already verified it)
     */
    public function verifyOtp(Request $request)
    {
      try {
          $request->validate([
              'mobile'        => 'required|string',
              'firebase_uid'  => 'required|string',
          ]);

        } catch (ValidationException $e) {

          return response()->json([
              'status' => false,
              'message' => $e->errors(),
          ], 422);
      }

        $user = User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not registered'
            ], 404);
        }

        // create temporary reset token (10 mins)
        $resetToken = bin2hex(random_bytes(32));

        Cache::put(
            'reset_pwd_' . $request->mobile,
            $resetToken,
            now()->addMinutes(10)
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully',
            'reset_token' => $resetToken
        ]);
    }

    /**
     * STEP 2: Reset Password
     */
    public function resetPassword(Request $request)
  {
      try {

          $request->validate([
              'mobile'        => 'required|string',
              'reset_token'   => 'required|string',
              'new_password'  => 'required|min:8|confirmed',
          ]);

      } catch (ValidationException $e) {

          return response()->json([
              'status' => false,
              'message' => $e->errors(),
          ], 422);
      }

      $cachedToken = Cache::get('reset_pwd_' . $request->mobile);

      if (!$cachedToken || $cachedToken !== $request->reset_token) {
          return response()->json([
              'status' => false,
              'message' => 'Invalid or expired reset token'
          ], 401);
      }

      $user = User::where('phone', $request->mobile)->first();

      if (!$user) {
          return response()->json([
              'status' => false,
              'message' => 'User not found'
          ], 404);
      }

      $user->password = Hash::make($request->new_password);
      $user->save();

      Cache::forget('reset_pwd_' . $request->mobile);

      return response()->json([
          'status' => true,
          'message' => 'Password reset successfully'
      ]);
  }
}
