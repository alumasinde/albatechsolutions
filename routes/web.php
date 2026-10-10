<?php

declare(strict_types=1);

use AlbaTech\Controllers\AdminController;
use AlbaTech\Controllers\EnquiryController;
use AlbaTech\Controllers\HomeController;
use AlbaTech\Controllers\GuideController;
use AlbaTech\Controllers\ServiceController;
use AlbaTech\Controllers\SeoController;
use AlbaTech\Controllers\StaticPageController;
use AlbaTech\Controllers\WorkController;
use AlbaTech\Core\Response;

/** @var \AlbaTech\Core\Application $app */
$app->router()->get('/', [new HomeController(), 'index']);
$app->router()->get('/services', [new ServiceController(), 'index']);
$app->router()->get('/services/{slug}', [new ServiceController(), 'show']);
$work = new WorkController();
$app->router()->get('/work', [$work, 'index']);
$app->router()->get('/work/{slug}', [$work, 'show']);
$app->router()->get('/about', [new StaticPageController(), 'about']);
$guides = new GuideController();
$app->router()->get('/guides', [$guides, 'index']);
$app->router()->get('/guides/{slug}', [$guides, 'show']);
$app->router()->get('/get-help', [new StaticPageController(), 'getHelp']);
$app->router()->post('/get-help', [new EnquiryController(), 'submit']);
$app->router()->get('/contact', [new StaticPageController(), 'getHelp']); // /contact redirects to /get-help
$app->router()->get('/contact-details', static function () use ($app): void { Response::redirect('/get-help'); }); // Merged into /get-help
$app->router()->get('/terms', [new StaticPageController(), 'terms']);
$app->router()->get('/privacy', [new StaticPageController(), 'privacy']);
$seo = new SeoController();
$app->router()->get('/sitemap.xml', [$seo, 'sitemap']);
$app->router()->get('/robots.txt', [$seo, 'robots']);
$app->router()->get('/llms.txt', [$seo, 'llms']);
$admin = new AdminController();
$app->router()->get('/admin/login', [$admin, 'login']);
$app->router()->post('/admin/login', [$admin, 'login']);
$app->router()->post('/admin/logout', [$admin, 'logout']);
$app->router()->get('/admin', [$admin, 'dashboard']);
$app->router()->get('/admin/services', [$admin, 'services']);
$app->router()->get('/admin/work', [$work, 'adminIndex']);
$app->router()->get('/admin/work/create', [$work, 'adminCreate']);
$app->router()->post('/admin/work/create', [$work, 'adminSave']);
$app->router()->get('/admin/work/{id}/edit', [$work, 'adminEdit']);
$app->router()->post('/admin/work/{id}/edit', [$work, 'adminSave']);
$app->router()->post('/admin/work/{id}/delete', [$work, 'adminDelete']);
$app->router()->get('/admin/services/create', [$admin, 'serviceCreate']);
$app->router()->post('/admin/services/create', [$admin, 'serviceSave']);
$app->router()->get('/admin/services/{id}/edit', [$admin, 'serviceEdit']);
$app->router()->post('/admin/services/{id}/edit', [$admin, 'serviceSave']);
$app->router()->post('/admin/services/{id}/toggle', [$admin, 'serviceToggle']);
$app->router()->post('/admin/services/{id}/feature', [$admin, 'serviceFeature']);
$app->router()->get('/admin/categories', [$admin, 'categories']);
$app->router()->post('/admin/categories', [$admin, 'categorySave']);
$app->router()->post('/admin/categories/{id}/edit', [$admin, 'categorySave']);
$app->router()->get('/admin/settings', [$admin, 'settings']);
$app->router()->post('/admin/settings', [$admin, 'settingsSave']);
$app->router()->get('/health', static function (): void { Response::html('OK'); });
