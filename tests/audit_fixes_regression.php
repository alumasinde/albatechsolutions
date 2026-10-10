<?php

declare(strict_types=1);

use AlbaTech\Core\Request;
use AlbaTech\Core\Router;
use AlbaTech\Core\View;

require dirname(__DIR__) . '/app/Core/Env.php';
require dirname(__DIR__) . '/app/Core/Database.php';
require dirname(__DIR__) . '/app/Core/Response.php';
require dirname(__DIR__) . '/app/Core/View.php';
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

$root = dirname(__DIR__);

// --- WCAG contrast guard for the colour pairs that previously failed axe -------------------------
$luminance = static function (string $hex): float {
    $hex = ltrim($hex, '#');
    $channels = array_map(
        static function (string $pair): float {
            $c = hexdec($pair) / 255;
            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        },
        str_split($hex, 2)
    );
    return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
};
$ratio = static function (string $a, string $b) use ($luminance): float {
    [$hi, $lo] = [max($luminance($a), $luminance($b)), min($luminance($a), $luminance($b))];
    return ($hi + 0.05) / ($lo + 0.05);
};
$tokens = (string) file_get_contents($root . '/public/assets/css/tokens.css');
$token = static function (string $name) use ($tokens): string {
    preg_match('/' . preg_quote($name, '/') . ':\s*(#[0-9a-fA-F]{6})/', $tokens, $m);
    return $m[1] ?? '';
};
foreach (['--color-text-muted', '--color-text-subtle', '--color-success', '--color-accent-text'] as $name) {
    $assert($token($name) !== '', "{$name} is defined in tokens.css");
    foreach (['#ffffff', '#f8fafc', '#f1f5f9'] as $surface) {
        $assert($ratio($token($name), $surface) >= 4.5, "{$name} has >= 4.5:1 contrast on {$surface}");
    }
}
$assert($ratio('#ffffff', $token('--color-success')) >= 4.5, 'White text on --color-success background is AA');
$assert($ratio('#052e16', '#25d366') >= 7.0, 'Dark text on WhatsApp green is AAA');

// --- CSS / layout structure ---------------------------------------------------------------------
$appCss = (string) file_get_contents($root . '/public/assets/css/app.css');
$assert(!preg_match('/^\s*@import\b/m', $appCss), 'app.css has no @import (avoids a serial CSS request chain)');
$layout = (string) file_get_contents($root . '/resources/views/layouts/main.php');
$assert(str_contains($layout, "View::asset('css/tokens.css')"), 'Layout links tokens.css directly');
$assert(!preg_match('/isset\(\s*\$service\s*\)/', $layout), 'Layout never derives page schema from a plain $service (loop-variable leak)');
$assert(str_contains($layout, '$schemaService'), 'Layout reads the explicit $schemaService flag');
$assert(!str_contains($layout, "fill='%23155eef'"), 'Layout no longer ships the off-brand inline favicon');
$assert(str_contains((string) file_get_contents($root . '/app/Controllers/ServiceController.php'), "'schemaService' => \$service"), 'ServiceController declares the service page for schema output');

// --- View::asset cache busting ------------------------------------------------------------------
$assert(preg_match('~^/assets/css/app\.css\?v=\d+$~', View::asset('css/app.css')) === 1, 'View::asset appends an mtime version');
$assert(View::asset('css/does-not-exist.css') === '/assets/css/does-not-exist.css', 'View::asset degrades gracefully for missing files');

// --- Router: HEAD is served by the GET route with no body ---------------------------------------
$router = new Router();
$called = 0;
$router->get('/probe', static function () use (&$called): void {
    $called++;
    echo 'BODY-SHOULD-NOT-APPEAR-ON-HEAD';
});
$_GET = [];
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'HEAD';
$_SERVER['REQUEST_URI'] = '/probe';
ob_start();
$router->dispatch(new Request());
$headOutput = (string) ob_get_clean();
$assert($called === 1, 'HEAD request reaches the matching GET handler');
$assert($headOutput === '', 'HEAD response body is discarded');

$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
$router->dispatch(new Request());
$getOutput = (string) ob_get_clean();
$assert($called === 2 && $getOutput === 'BODY-SHOULD-NOT-APPEAR-ON-HEAD', 'GET still returns the body');

// --- Router redirect pattern is syntactically valid and rejects dangerous paths -----------------
$routerSource = (string) file_get_contents($root . '/app/Core/Router.php');
$assert(preg_match("/preg_match\\('(#[^']*(?:\\\\'[^']*)*#)'/", $routerSource, $m) === 1, 'Trailing-slash allow-list pattern is present');
$pattern = str_replace("\\'", "'", $m[1]);
$assert(@preg_match($pattern, '/services/') === 1, 'Trailing-slash pattern compiles and accepts normal paths');
$assert(preg_match($pattern, '/\\evil.com/') === 0, 'Pattern rejects backslash paths (open-redirect guard)');
$assert(preg_match($pattern, "/services/\r\nSet-Cookie: x=1") === 0, 'Pattern rejects control characters (header-injection guard)');

// --- .htaccess performance blocks ---------------------------------------------------------------
$htaccess = (string) file_get_contents($root . '/public/.htaccess');
$assert(str_contains($htaccess, 'mod_deflate') && str_contains($htaccess, 'mod_expires'), '.htaccess enables compression and asset caching');

echo "PASS: {$checks} audit-fix regression checks.\n";
