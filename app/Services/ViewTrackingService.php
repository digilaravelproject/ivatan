<?php

namespace App\Services;

use App\Models\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ViewTrackingService
{
    public function track($model, Request $request): bool
    {
        try {
            $userId = Auth::id();
            $ip = $request->ip();

            $exists = View::where('viewable_id', $model->id)
                ->where('viewable_type', get_class($model))
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->when(!$userId, fn($q) => $q->where('ip_address', $ip))
                ->exists();

            if ($exists) {
                return false; // Duplicate view
            }

            View::create([
                'user_id' => $userId,
                'viewable_id' => $model->id,
                'viewable_type' => get_class($model),
                'ip_address' => $ip,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("View tracking failed: " . $e->getMessage());
            return false;
        }
    }
}
