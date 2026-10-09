<?php

declare(strict_types=1);

use AlbaTech\Controllers\HomeController;
use AlbaTech\Controllers\ServiceController;
use AlbaTech\Controllers\StaticPageController;
use AlbaTech\Core\Response;

/** @var \AlbaTech\Core\Application $app */
$app->router()->get('/', [new HomeController(), 'index']);
$app->router()->get('/services', [new ServiceController(), 'index']);
$app->router()->get('/services/{slug}', [new ServiceController(), 'show']);
$app->router()->get('/about', [new StaticPageController(), 'about']);
$app->router()->get('/guides', [new StaticPageController(), 'guides']);
$app->router()->get('/get-help', [new StaticPageController(), 'getHelp']);
$app->router()->get('/health', static function (): void {
    Response::html('OK');
});
