<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\Router;

// This file always sits in the true document root — whether the
// folder is locally named "public/" or renamed "public_html/" on
// deployment, __DIR__ here is correct either way. Anything that needs
// to write a publicly web-reachable file (e.g. MediaService) should
// use this constant instead of computing a relative path with a
// hardcoded folder name, which breaks the moment the folder is renamed.
define('PUBLIC_PATH', __DIR__);

require dirname(__DIR__) . '/app/bootstrap.php';

$router = new Router();

// Route definitions register themselves onto $router.
require dirname(__DIR__) . '/routes/web.php';

$request = new Request();
$response = $router->dispatch($request);
$response->send();
