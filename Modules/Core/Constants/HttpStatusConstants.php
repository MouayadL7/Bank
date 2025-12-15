<?php

declare(strict_types=1);

namespace Modules\Core\Constants;

/**
 * HTTP Status Code Constants
 */
final class HttpStatusConstants
{
    // Success Codes (2xx)
    public const HTTP_200_OK = 200;

    public const HTTP_201_CREATED = 201;

    public const HTTP_202_ACCEPTED = 202;

    public const HTTP_204_NO_CONTENT = 204;

    // Client Error Codes (4xx)
    public const HTTP_400_BAD_REQUEST = 400;

    public const HTTP_401_UNAUTHORIZED = 401;

    public const HTTP_403_FORBIDDEN = 403;

    public const HTTP_404_NOT_FOUND = 404;

    public const HTTP_405_METHOD_NOT_ALLOWED = 405;

    public const HTTP_409_CONFLICT = 409;

    public const HTTP_422_UNPROCESSABLE_ENTITY = 422;

    public const HTTP_429_TOO_MANY_REQUESTS = 429;

    // Server Error Codes (5xx)
    public const HTTP_500_INTERNAL_SERVER_ERROR = 500;

    public const HTTP_502_BAD_GATEWAY = 502;

    public const HTTP_503_SERVICE_UNAVAILABLE = 503;

    public const HTTP_504_GATEWAY_TIMEOUT = 504;
}
