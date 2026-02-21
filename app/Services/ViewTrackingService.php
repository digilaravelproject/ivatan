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
        // Initialize variables
        $userId = Auth::id();  // Can be null for guests
        $ip = $request->ip();  // Get the IP address

        try {
            // Check for duplicate view
            if ($this->isDuplicateView($model, $userId, $ip)) {
                return false;
            }

            // Record the view
            $this->createViewRecord($model, $userId, $ip);

            // Increment the view count if applicable
            $this->incrementViewCount($model);

            return true;
        } catch (\Exception $e) {
            // Log the error details
            Log::error("View tracking failed: " . $e->getMessage(), [
                'model' => $model,
                'user_id' => $userId ?? 'guest',
                'ip_address' => $ip,
                'exception' => $e,
            ]);
            return false;
        }
    }

    // Check if the view is a duplicate
    private function isDuplicateView($model, $userId, $ip): bool
    {
        return View::where('viewable_id', $model->id)
            ->where('viewable_type', get_class($model))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('ip_address', $ip))
            ->exists();
    }

    // Create the view record in the database
    private function createViewRecord($model, $userId, $ip): void
    {
        View::create([
            'user_id' => $userId,
            'viewable_id' => $model->id,
            'viewable_type' => get_class($model),
            'ip_address' => $ip,
        ]);
    }

    // Increment view count if applicable
    private function incrementViewCount($model): void
    {
        $model->increment('view_count');
        // Force reload to reflect updated view_count
        $model->refresh();
    }
    public function getUserLogs(int $userId, int $perPage = 15)
    {
        return \DB::table('activity_log')
            ->where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
