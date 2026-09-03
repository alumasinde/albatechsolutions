<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

/**
 * Usage in routes: middleware => ['App\Core\Middleware\RbacMiddleware:orders.view']
 */
final class RbacMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next, ?string $param = null): Response
    {
        if ($param === null) {
            return Response::text('Server misconfiguration: no permission specified.', 500);
        }

        if (!Auth::can($param)) {
            if ($request->isAjax()) {
                return Response::json(['message' => 'Forbidden.'], 403);
            }

            return Response::view('errors.403', [], 403);
        }

        return $next($request);
    }
}
