<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;

/**
 * Rate-limit bearer-token routes by both the source IP and the bearer token.
 * Only the long opaque route token is used as a key; short human references
 * such as AT-HLP-* are never authorization credentials.
 */
final class TokenRateLimitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next, ?string $param = null): Response
    {
        $bucket = $param ?: 'token';
        $token = trim((string) $request->param('token'));
        if ($token === '' || strlen($token) < 32) {
            return Response::text('Not found', 404);
        }

        $ipKey = $bucket . ':ip:' . $request->ip();
        $tokenKey = $bucket . ':token:' . hash('sha256', $token);
        $maxAttempts = max(5, (int) ($_ENV['TOKEN_RATE_LIMIT_MAX_ATTEMPTS'] ?? 30));
        $decayMinutes = max(1, (int) ($_ENV['TOKEN_RATE_LIMIT_DECAY_MINUTES'] ?? 1));

        if (!$this->consume($ipKey, $maxAttempts, $decayMinutes) || !$this->consume($tokenKey, $maxAttempts, $decayMinutes)) {
            Logger::security('Bearer token route rate limit exceeded.', ['bucket' => $bucket, 'ip' => $request->ip()]);
            return Response::json(['message' => 'Too many requests. Please slow down.'], 429, ['Cache-Control' => 'no-store']);
        }

        return $next($request);
    }

    private function consume(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT attempts, window_started_at FROM rate_limits WHERE `key` = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        if (!$row) {
            $pdo->prepare('INSERT INTO rate_limits (`key`, attempts, window_started_at) VALUES (:key, 1, NOW())')
                ->execute(['key' => $key]);
            return true;
        }

        if (time() - strtotime((string) $row['window_started_at']) > ($decayMinutes * 60)) {
            $pdo->prepare('UPDATE rate_limits SET attempts = 1, window_started_at = NOW() WHERE `key` = :key')
                ->execute(['key' => $key]);
            return true;
        }

        if ((int) $row['attempts'] >= $maxAttempts) return false;

        $pdo->prepare('UPDATE rate_limits SET attempts = attempts + 1 WHERE `key` = :key')
            ->execute(['key' => $key]);
        return true;
    }
}
