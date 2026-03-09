<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ApiResponseTrait
{
    /**
     * Standard success response
     */
    protected function successResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Standard error response
     */
    protected function errorResponse(string $message, int $code = 500, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Execute logic within a DB transaction with pessimistic locking support
     */
    protected function executeSecure(callable $callback, string $errorLogMessage = 'Secure Transaction Failed')
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction($callback);
        } catch (\Throwable $e) {
            Log::error($errorLogMessage, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('An unexpected error occurred. Please try again later.', 500);
        }
    }
}
