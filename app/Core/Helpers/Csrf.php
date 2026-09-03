<?php

declare(strict_types=1);

namespace App\Core\Helpers;

use App\Core\Session;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::SESSION_KEY)) {
            Session::put(self::SESSION_KEY, bin2hex(random_bytes(32)));
        }

        return Session::get(self::SESSION_KEY);
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');

        return "<input type=\"hidden\" name=\"_csrf_token\" value=\"{$token}\">";
    }

    public static function verify(?string $submittedToken): bool
    {
        $sessionToken = Session::get(self::SESSION_KEY);

        if (!$sessionToken || !$submittedToken) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }
}
