<?php

declare(strict_types=1);

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Request;
use AlbaTech\Core\Router;

require dirname(__DIR__) . '/app/Core/Env.php';
require dirname(__DIR__) . '/app/Core/Response.php';
require dirname(__DIR__) . '/app/Core/View.php';
require dirname(__DIR__) . '/app/Core/Database.php';
require dirname(__DIR__) . '/app/Core/AdminSecurity.php';
require dirname(__DIR__) . '/app/Core/Request.php';
require dirname(__DIR__) . '/app/Core/Router.php';

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
};

// Use a dedicated CLI session for deterministic CSRF middleware tests.
ini_set('session.save_path', sys_get_temp_dir());
AdminSecurity::startSession();
$token = AdminSecurity::csrfToken();
$assert(strlen($token) === 64 && ctype_xdigit($token), 'CSRF token is 256-bit hex');
$assert(AdminSecurity::verifyCsrf($token), 'Correct CSRF token verifies');
$assert(!AdminSecurity::verifyCsrf($token . 'tampered'), 'Tampered CSRF token is rejected');

$router = new Router();
$called = false;
$router->post('/admin/test-mutation', static function () use (&$called): void { $called = true; });
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/admin/test-mutation';
$_POST = ['_csrf' => 'invalid'];
$_GET = [];
http_response_code(200);
ob_start();
$router->dispatch(new Request());
$output = (string) ob_get_clean();
$assert(!$called, 'Admin POST with invalid CSRF does not invoke handler');
$assert(http_response_code() === 419, 'Invalid CSRF request returns HTTP 419');
$assert(str_contains($output, 'Request verification failed'), 'Invalid CSRF response renders safe error page');

$_POST = ['_csrf' => $token];
$_SERVER['REQUEST_URI'] = '/admin/test-mutation';
$router->dispatch(new Request());
$assert($called, 'Admin POST with valid CSRF token reaches handler');

// Verify strict CSP and nonce wiring statically, without depending on a running web server.
$index = file_get_contents(dirname(__DIR__) . '/public_html/index.php');
$assert(is_string($index) && str_contains($index, "script-src 'self' 'nonce-" ), 'CSP is configured dynamically rather than with a hard-coded nonce');
$assert(is_string($index) && !str_contains($index, "script-src 'self' 'unsafe-inline'"), 'CSP does not allow unsafe inline scripts');
$adminLayout = file_get_contents(dirname(__DIR__) . '/resources/views/layouts/admin.php');
$assert(is_string($adminLayout) && str_contains($adminLayout, 'nonce="<?= View::escape($cspNonce'), 'Admin inline script carries CSP nonce');

// Check admin source for common dynamic-SQL concatenation patterns.
$controller = file_get_contents(dirname(__DIR__) . '/app/Controllers/AdminController.php');
$assert(is_string($controller) && preg_match('/->(?:query|exec)\s*\(\s*[\"\'][^\"\']*\$[A-Za-z_]/s', $controller) !== 1, 'Admin controller has no obvious interpolated query/exec calls');

AdminSecurity::destroySession();
fwrite(STDOUT, "PASS: {$checks} Phase 1 security regression checks.\n");
