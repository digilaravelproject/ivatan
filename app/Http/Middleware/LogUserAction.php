<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogUserAction
{
    protected $activity;

    /**
     * Create a new middleware instance.
     *
     * @param  \App\Services\ActivityService  $activity
     * @return void
     */
    public function __construct(ActivityService $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $user = $request->user();
            if (!$user) {
                return $response; // No user, no need to log
            }

            $routeName = $request->route() ? $request->route()->getName() : null;
            $path = $request->path();
            $method = $request->method();

            // Log Search Activity
            if ($this->isSearchRoute($routeName, $path)) {
                $term = $request->input('q') ?? $request->input('term');
                if ($term) {
                    $this->activity->logSearch($user, $term);
                }
            }

            // Log Post Like Interaction
            if ($this->isLikeRoute($method, $path)) {
                preg_match('#api/posts/(\d+)/like#', $path, $matches);
                $postId = $matches[1] ?? null;
                if ($postId) {
                    $this->activity->logInteraction($user, 'like', null, ['post_id' => $postId]);
                }
            }

            // Generic CRUD Activity Logging
            if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
                $this->logCrudActivity($request, $routeName, $method, $path, $user);
            }
        } catch (\Exception $e) {
            report($e); // Log the error for debugging
        }

        return $response;
    }

    /**
     * Check if the route is related to search.
     *
     * @param string|null $routeName
     * @param string $path
     * @return bool
     */
    protected function isSearchRoute(?string $routeName, string $path): bool
    {
        return $routeName === 'api.search' || Str::contains($path, '/search');
    }

    /**
     * Check if the route is related to post like.
     *
     * @param string $method
     * @param string $path
     * @return bool
     */
    protected function isLikeRoute(string $method, string $path): bool
    {
        return $method === 'POST' && preg_match('#api/posts/\d+/like#', $path);
    }

    /**
     * Log CRUD operations.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null $routeName
     * @param string $method
     * @param string $path
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    protected function logCrudActivity(Request $request, ?string $routeName, string $method, string $path, $user)
    {
        // Sanitize sensitive fields before logging
        $payload = $request->except(['password', 'password_confirmation', '_token']);
        $this->activity->log("API: {$method} {$path}", [
            'route' => $routeName,
            'payload_keys' => array_keys($payload),
        ], 'api', $user);

        // Optionally log request body (not sensitive data) for debugging
        Log::debug('API Request Data:', [
            'user_id' => $user->id,
            'method' => $method,
            'route' => $routeName,
            'path' => $path,
            'data' => $payload, // Only log non-sensitive data
        ]);
    }
}
