<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class ActivityService
{
    protected string $defaultLog = 'app';

    /**
     * Generic logger wrapper.
     *
     * @param string $description
     * @param array $properties
     * @param string|null $logName
     * @param \Illuminate\Database\Eloquent\Model|int|string|null $causer
     * @param mixed|null $subject
     * @return Activity
     */
    public function log(
        string $description,
        array $properties = [],
        ?string $logName = null,
        ?Authenticatable $causer = null,
        $subject = null
    ): Activity {
        $log = $logName ?? $this->defaultLog;

        $activity = activity($log)
            ->withProperties(array_merge([
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ], $properties));

        if ($subject) {
            $activity = $activity->performedOn($subject);
        }

        if ($causer) {
            $activity = $activity->causedBy($causer);
        } elseif (Auth::check()) {
            $activity = $activity->causedBy(Auth::user());
        }

        return $activity->log($description);
    }

    /**
     * Log user login activity.
     *
     * @param Authenticatable $user
     * @return Activity
     */
    public function logLogin(Authenticatable $user): Activity
    {
        return $this->log('User logged in', ['method' => 'login'], 'user', $user);
    }

    /**
     * Log user logout activity.
     *
     * @param Authenticatable $user
     * @return Activity
     */
    public function logLogout(Authenticatable $user): Activity
    {
        return $this->log('User logged out', ['method' => 'logout'], 'user', $user);
    }

    /**
     * Log user search activity.
     *
     * @param Authenticatable|null $user
     * @param string $term
     * @param array $extra
     * @return Activity
     */
    public function logSearch(?Authenticatable $user, string $term, array $extra = []): Activity
    {
        $user = $user ?? Auth::user();

        return $this->log('User searched', array_merge(['term' => $term], $extra), 'search', $user);
    }

    /**
     * Log user interaction activity.
     *
     * @param Authenticatable|null $user
     * @param string $type
     * @param mixed|null $subject
     * @param array $extra
     * @return Activity
     */
    public function logInteraction(?Authenticatable $user, string $type, $subject = null, array $extra = []): Activity
    {
        $user = $user ?? Auth::user();
        $description = "User performed interaction: " . ucfirst($type);

        return $this->log($description, array_merge(['interaction_type' => $type], $extra), 'interaction', $user, $subject);
    }
    /**
     * Batch-insert rows into activity_log for high-throughput events (views)
     * rows: array of arrays with keys: log_name, description, subject_type, subject_id, causer_type, causer_id, properties
     */
    public function batchInsert(array $rows)
    {
        foreach (array_chunk($rows, 100) as $chunk) {
            $now = now();
            $insert = array_map(function ($r) use ($now) {
                return [
                    'log_name' => $r['log_name'] ?? 'app',
                    'description' => $r['description'] ?? null,
                    'subject_type' => $r['subject_type'] ?? null,
                    'subject_id' => $r['subject_id'] ?? null,
                    'causer_type' => $r['causer_type'] ?? null,
                    'causer_id' => $r['causer_id'] ?? null,
                    'properties' => json_encode($r['properties'] ?? []),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $chunk);

            \DB::table('activity_log')->insert($insert);
        }
    }
    /**
     * Get paginated logs for a specific user.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLogsForUser(int $userId, int $perPage = 30)
    {
        return Activity::where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Query activities with filters for admin panel.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder<\Spatie\Activitylog\Models\Activity>
     */
    public function adminQuery(array $filters = []): Builder
    {
        $query = Activity::query();

        if (!empty($filters['log_name'])) {
            $query->where('log_name', $filters['log_name']);
        }
        if (!empty($filters['causer_id'])) {
            $query->where('causer_id', $filters['causer_id']);
        }
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }
        if (!empty($filters['from'])) {
            // Ensure 'from' is a valid date string or DateTime
            $query->where('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            // Ensure 'to' is a valid date string or DateTime
            $query->where('created_at', '<=', $filters['to']);
        }

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        return $query->orderBy('created_at', 'desc');
    }
}
