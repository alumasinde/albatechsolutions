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

        // Super Admin is the system owner and must not be locked out by a
        // missing email verification timestamp or incomplete role_permissions.
        // The account still has to be authenticated and assigned the
        // super-admin role through user_roles.
        $isSuperAdmin = $user !== null && Auth::isSuperAdmin();

        if ($user === null || (!$isSuperAdmin && (empty($user['email_verified_at']) || !Auth::hasStaffRole()))) {
            if ($request->isAjax()) {
                return Response::json(['message' => 'Admin access requires a verified staff account.'], 403);
            }

            return Response::view('errors.403', [], 403);
        }

        return $next($request);
    }
}
