<?php

namespace App\Traits;

use App\Services\ActivityService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

trait ActivityLoggerTrait
{
    /**
     * Resolve the ActivityService instance.
     */
    protected function activitySvc(): ActivityService
    {
        return App::make(ActivityService::class);
    }

    /**
     * Log a generic activity.
     *
     * @param string $description
     * @param array $properties
     * @param string|null $logName
     * @param mixed|null $causer
     * @param mixed|null $subject
     * @return Activity
     */
    protected function logActivity(
        string $description,
        array $properties = [],
        ?string $logName = null,
        $causer = null,
        $subject = null
    ): Activity {
        return $this->activitySvc()->log($description, $properties, $logName, $causer, $subject);
    }

    /**
     * Log an interaction activity for the authenticated user.
     *
     * @param string $type
     * @param mixed|null $subject
     * @param array $extra
     * @return Activity|null
     */
    protected function logInteraction(string $type, $subject = null, array $extra = []): ?Activity
    {
        $user = auth()->user();

        if (!$user) {
            // Warning log jab authenticated user na mile
            Log::warning("Attempted to log interaction '{$type}' but no authenticated user found.");

            // Return null to silently skip logging
            return null;
        }

        return $this->activitySvc()->logInteraction($user, $type, $subject, $extra);
    }
}
