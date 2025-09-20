<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = [], $message = 'Success', $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error($message = 'Something went wrong', $status = 500, $errors = [])
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
