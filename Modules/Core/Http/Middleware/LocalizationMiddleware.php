<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming HTTP request
     * @param Closure(Request): Response $next The next middleware in the pipeline
     * @return Response The HTTP response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var string $defaultLanguage */
        $defaultLanguage = config('languages.default', 'ar');

        /** @var string $headerLanguage */
        $headerLanguage = $request->header('Accept-Language', $defaultLanguage);

        /** @var string $language */
        $language = strtolower(substr($headerLanguage, 0, 2));

        /** @var array<int, string> $supported */
        $supported = array_keys(config('languages.supported'));

        if (!in_array($language, $supported, true)) {
            $language = $defaultLanguage;
        }

        app()->setLocale($language);
        Carbon::setLocale($language);
        $request->merge(['locale' => $language]);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Content-Language', $language);

        return $response;
    }
}
