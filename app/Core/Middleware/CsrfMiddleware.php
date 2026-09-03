<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Helpers\Csrf;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;

final class CsrfMiddleware implements MiddlewareInterface
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, callable $next, ?string $param = null): Response
    {
        if (in_array($request->method(), self::SAFE_METHODS, true)) {
            return $next($request);
        }

        // CSRF is a browser/session concern. Only accept the explicit
        // `_csrf_token` request field; API bearer tokens are never CSRF tokens.
        $token = $request->input('_csrf_token');

        if (!Csrf::verify(is_string($token) ? $token : null)) {
            Logger::security('CSRF token mismatch', ['ip' => $request->ip(), 'path' => $request->path()]);

            if ($request->isAjax()) {
                return Response::json(['message' => 'Invalid or expired security token. Please refresh and try again.'], 419);
            }

            return Response::view('errors.419', [], 419);
        }

        return $next($request);
    }
}
