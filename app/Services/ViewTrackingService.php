<?php

namespace App\Services;

use App\Models\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ViewTrackingService
{
    public function track($model, Request $request): bool
    {
        $userId = Auth::id();
        $ip = $request->ip();

        try {
            return DB::transaction(function () use ($model, $userId, $ip) {
                $locked = $model->newQuery()
                    ->whereKey($model->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($this->isDuplicateView($locked, $userId, $ip)) {
                    return false;
                }

                $this->createViewRecord($locked, $userId, $ip);
                $this->incrementViewCount($locked);

                return true;
            });
        } catch (\Exception $e) {
            Log::error("View tracking failed: " . $e->getMessage(), [
                'model'    => $model,
                'user_id'  => $userId ?? 'guest',
                'ip'       => $ip,
                'exception' => $e,
            ]);
            return false;
        }
    }

    private function isDuplicateView($model, $userId, $ip): bool
    {
        return View::where('viewable_id', $model->id)
            ->whereIn('viewable_type', [$model->getMorphClass(), get_class($model)])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('ip_address', $ip))
            ->where('created_at', '>=', now()->subDay())
            ->exists();
    }

    private function createViewRecord($model, $userId, $ip): void
    {
        View::create([
            'user_id'      => $userId,
            'viewable_id'  => $model->id,
            'viewable_type' => $model->getMorphClass(),
            'ip_address'   => $ip,
        ]);
    }

    private function incrementViewCount($model): void
    {
        $model->increment('view_count');
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
