<?php

declare(strict_types=1);

namespace Modules\Core\Helpers;

use Illuminate\Http\JsonResponse;
use Modules\Core\Constants\HttpStatusConstants;

final class ApiResponse
{
    public static function success(string $message = 'SUCCESS', int $statusCode = HttpStatusConstants::HTTP_200_OK, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    public static function error(string $message = 'ERROR', int $statusCode = HttpStatusConstants::HTTP_400_BAD_REQUEST, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data,
            'status_code' => $statusCode,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }
}
