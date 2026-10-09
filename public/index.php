<?php

declare(strict_types=1);

use AlbaTech\Core\Application;
use AlbaTech\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

date_default_timezone_set(Env::get('APP_TIMEZONE', 'Africa/Nairobi'));

$app = new Application();
require dirname(__DIR__) . '/routes/web.php';

$app->run();
