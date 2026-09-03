<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next, ?string $param = null): Response
    {
        $user = Auth::user();

        if ($user === null || empty($user['email_verified_at']) || !Auth::hasStaffRole()) {
            if ($request->isAjax()) {
                return Response::json(['message' => 'Admin access requires a verified staff account.'], 403);
            }

            return Response::view('errors.403', [], 403);
        }

        return $next($request);
    }
}
