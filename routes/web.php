<?php

declare(strict_types=1);

use AlbaTech\Controllers\HomeController;

/** @var \AlbaTech\Core\Application $app */
$app->router()->get('/', [new HomeController(), 'index']);
$app->router()->get('/health', static function (): void {
    \AlbaTech\Core\Response::html('OK');
});
