<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserSellerController extends Controller
{
    public function toggleSelf(Request $request)
    {
        $user = $request->user();
        $user->is_seller = !$user->is_seller;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_seller ? 'You are now a seller' : 'You are no longer a seller',
            'is_seller' => $user->is_seller,
        ]);
    }
}
