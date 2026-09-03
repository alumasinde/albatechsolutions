<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class CustomerMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next, ?string $param = null): Response
    {
        if (!Auth::check()) {
            \App\Core\Session::put('_intended_url', $request->fullUrl());
            return Response::redirect('/login');
        }

        if (!Auth::hasRole('customer') || Auth::can('users.view')) {
            return Response::text('Forbidden', 403);
        }

        return $next($request);
    }
}
