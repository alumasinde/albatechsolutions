<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;

/**
 * Simple DB-backed rate limiter. $param can override the bucket
 * name, e.g. 'login' for stricter limits on auth routes.
 */
final class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next, ?string $param = null): Response
    {
        $bucket = $param ?? 'default';
        $maxAttempts = (int) ($_ENV['RATE_LIMIT_MAX_ATTEMPTS'] ?? 60);
        $decayMinutes = (int) ($_ENV['RATE_LIMIT_DECAY_MINUTES'] ?? 1);

        $key = $bucket . ':' . $request->ip();
        $pdo = Database::connection();

        $stmt = $pdo->prepare('SELECT attempts, window_started_at FROM rate_limits WHERE `key` = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        $now = time();

        if (!$row) {
            $pdo->prepare('INSERT INTO rate_limits (`key`, attempts, window_started_at) VALUES (:key, 1, NOW())')
                ->execute(['key' => $key]);

            return $next($request);
        }

        $windowStarted = strtotime($row['window_started_at']);

        if ($now - $windowStarted > $decayMinutes * 60) {
            $pdo->prepare('UPDATE rate_limits SET attempts = 1, window_started_at = NOW() WHERE `key` = :key')
                ->execute(['key' => $key]);

            return $next($request);
        }

        if ((int) $row['attempts'] >= $maxAttempts) {
            Logger::security('Rate limit exceeded', ['key' => $key, 'ip' => $request->ip()]);

            return Response::json(['message' => 'Too many requests. Please slow down.'], 429);
        }

        $pdo->prepare('UPDATE rate_limits SET attempts = attempts + 1 WHERE `key` = :key')
            ->execute(['key' => $key]);

        return $next($request);
    }
}
