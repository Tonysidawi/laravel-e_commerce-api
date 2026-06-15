<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Return a successful response.
     */
    public function success($data = [], string $message = '', $responseCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $responseCode);
    }

    /**
     * Return an error response.
     */
    public function error($data = [], string $message = '', $responseCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $responseCode);
    }
}
