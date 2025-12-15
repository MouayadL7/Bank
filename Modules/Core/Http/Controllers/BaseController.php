<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Constants\HttpStatusConstants;
use Modules\Core\Constants\CoreMessageConstants;
use Modules\Core\Helpers\ApiResponse;
use Modules\Core\Http\Controllers\Controller;

/**
 * Base API controller class that all API controllers extend from
 *
 * @apiResponseField success boolean The status of the request (true/false)
 * @apiResponseField message string A message describing the result
 * @apiResponseField data object|null The response data
 * @apiResponseField status_code integer HTTP status code
 * @apiResponseField timestamp string ISO 8601 timestamp
 *
 * @responseFile 500 responses/error_response.json
 * @responseFile 400 responses/validation_error.json
 */
abstract class BaseController extends Controller
{
    public function __construct(private readonly Request $request) {}

    /**
     * Returns a paginated response
     *
     * @template T
     *
     * @param  LengthAwarePaginator<T>  $paginator
     * @return JsonResponse<array{
     *     success: true,
     *     message: string,
     *     data: array<int, T>,
     *     meta: array{
     *         current_page: int,
     *         from: int|null,
     *         last_page: int,
     *         per_page: int,
     *         to: int|null,
     *         total: int,
     *     },
     *     status_code: int,
     *     timestamp: string
     * }>
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        ?string $message = null,
        int $statusCode = HttpStatusConstants::HTTP_200_OK,
    ): JsonResponse {
        return $this->successResponse(
            message: $message,
            statusCode: $statusCode,
            data: [
                'items' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ],
        );
    }

    /**
     * Returns a success response with optional data
     *
     * @template TResponseData
     *
     * @param  string|null  $message  Custom success message
     * @param  int  $statusCode  HTTP status code
     * @param  TResponseData|null  $data  Response data
     * @return JsonResponse<array{
     *   success: true,
     *   message: string,
     *   data: TResponseData|null,
     *   status_code: int,
     *   timestamp: string
     * }>
     */
    protected function successResponse(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = HttpStatusConstants::HTTP_200_OK,
    ): JsonResponse {
        return ApiResponse::success(
            message: $message ?? CoreMessageConstants::get(CoreMessageConstants::GENERIC_SUCCESS),
            statusCode: $statusCode,
            data: $data,
        );
    }

    /**
     * Returns an error response with optional error data
     *
     * @template TErrorData
     *
     * @param  string|null  $message  Custom error message
     * @param  int  $statusCode  HTTP status code
     * @param  TErrorData|null  $data  Error data
     * @return JsonResponse<array{
     *   success: false,
     *   message: string,
     *   errors: TErrorData|null,
     *   status_code: int,
     *   timestamp: string
     * }>
     */
    protected function errorResponse(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = HttpStatusConstants::HTTP_400_BAD_REQUEST,
    ): JsonResponse {
        return ApiResponse::error(
            message: $message ?? CoreMessageConstants::get(CoreMessageConstants::GENERIC_ERROR),
            statusCode: $statusCode,
            data: $data,
        );
    }
}
