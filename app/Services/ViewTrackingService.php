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
                $locked = get_class($model)::withoutEvents(function () use ($model) {
                    return $model->newQuery()
                        ->whereKey($model->id)
                        ->lockForUpdate()
                        ->firstOrFail();
                });

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
    }

    public function trackMultiple($models, Request $request): void
    {
        if (empty($models)) {
            return;
        }

        $userId = Auth::id();
        $ip = $request->ip();

        try {
            $models = collect($models);
            $modelIds = $models->pluck('id')->filter()->toArray();
            if (empty($modelIds)) {
                return;
            }

            $firstModel = $models->first();
            $morphClass = $firstModel->getMorphClass();
            $className = get_class($firstModel);

            $existingViews = View::whereIn('viewable_id', $modelIds)
                ->whereIn('viewable_type', [$morphClass, $className])
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->when(!$userId, fn($q) => $q->where('ip_address', $ip))
                ->where('created_at', '>=', now()->subDay())
                ->pluck('viewable_id')
                ->toArray();

            $nonDuplicateModels = $models->filter(function ($model) use ($existingViews) {
                return !in_array($model->id, $existingViews);
            });

            if ($nonDuplicateModels->isEmpty()) {
                return;
            }

            DB::transaction(function () use ($nonDuplicateModels, $userId, $ip, $morphClass) {
                $records = [];
                $now = now();
                foreach ($nonDuplicateModels as $model) {
                    $records[] = [
                        'user_id' => $userId,
                        'viewable_id' => $model->id,
                        'viewable_type' => $morphClass,
                        'ip_address' => $ip,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $model->newQuery()->whereKey($model->id)->increment('view_count');
                }
                View::insert($records);
            });
        } catch (\Exception $e) {
            Log::error("Bulk view tracking failed: " . $e->getMessage(), [
                'user_id' => $userId ?? 'guest',
                'ip' => $ip,
                'exception' => $e,
            ]);
        }
    }

    public function getUserLogs(int $userId, int $perPage = 15)
    {
        return DB::table('activity_log')
            ->where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
