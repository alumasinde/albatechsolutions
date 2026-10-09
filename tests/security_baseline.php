<?php

declare(strict_types=1);

use AlbaTech\Core\RateLimiter;
use AlbaTech\Core\Request;

require dirname(__DIR__) . '/app/Core/Response.php';
require dirname(__DIR__) . '/app/Core/Env.php';
require dirname(__DIR__) . '/app/Core/View.php';
require dirname(__DIR__) . '/app/Core/Database.php';
require dirname(__DIR__) . '/app/Core/RateLimiter.php';
require dirname(__DIR__) . '/app/Core/Request.php';

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
};

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/admin/services';
$_POST = ['name' => ['unexpected', 'array']];
$_GET = [];
$request = new Request();
$assert($request->input('name', '') === '', 'Request rejects array input');
$assert($request->path() === '/admin/services', 'Request normalizes route path');

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$key = RateLimiter::key('test-security-baseline', 'admin@example.test');
RateLimiter::clear($key);
$assert(!RateLimiter::tooManyAttempts($key, 3, 900), 'Fresh limiter key is not blocked');
RateLimiter::hit($key, 900);
RateLimiter::hit($key, 900);
$assert(!RateLimiter::tooManyAttempts($key, 3, 900), 'Limiter allows attempts below threshold');
RateLimiter::hit($key, 900);
$assert(RateLimiter::tooManyAttempts($key, 3, 900), 'Limiter blocks at threshold');
RateLimiter::clear($key);

fwrite(STDOUT, "PASS: {$checks} security baseline checks.\n");
