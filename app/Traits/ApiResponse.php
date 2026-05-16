<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Trait ApiResponse
 *
 * Provides a standardized JSON API response format across controllers.
 *
 * @package App\Traits
 */
trait ApiResponse
{
    /**
     * Return a standardized success JSON response.
     *
     * @param  mixed        $data     Data payload to return (default: empty array)
     * @param  string|null  $message  Custom success message (default: "Success")
     * @param  int          $status   HTTP status code (default: 200)
     * @return JsonResponse
     */
    protected function success($data = [], string $message = 'Success', int $status = 200): JsonResponse
    {
        // Ensure $data is array or object (avoid null issues)
        if (is_null($data)) {
            $data = [];
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param  string       $message  Error message to show
     * @param  int          $status   HTTP status code (default: 500)
     * @param  array|string $errors   Validation or detailed errors (optional)
     * @return JsonResponse
     */
    protected function error(string $message = 'Something went wrong', int $status = 500, $errors = []): JsonResponse
    {
        // Normalize $errors to an array for consistency
        if (!is_array($errors)) {
            $errors = [$errors];
        }

        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * Handle unexpected exceptions gracefully and log the error.
     *
     * @param  Throwable  $e
     * @param  string     $fallbackMessage
     * @return JsonResponse
     */
    protected function exceptionResponse(Throwable $e, string $fallbackMessage = 'An unexpected error occurred'): JsonResponse
    {
        \Log::error('API Exception: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return $this->error($fallbackMessage, 500);
    }
}
