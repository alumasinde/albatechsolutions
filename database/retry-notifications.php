<?php

declare(strict_types=1);

/**
 * Retry failed assistance notifications whose backoff window has elapsed.
 * Run from cron every 5 minutes in production.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

\App\Core\Container::boot();
$repository = \App\Core\Container::resolve(\App\Modules\Assistance\Repository\AssistanceNotificationRepository::class);
$service = \App\Core\Container::resolve(\App\Modules\Assistance\Service\AssistanceNotificationService::class);
$maxAttempts = max(1, (int)\App\Core\Config::get('notifications.retry.max_attempts', 3));

$items = $repository->retryable($maxAttempts, 50);
foreach ($items as $item) {
    $ok = $service->retry((int)$item['id']);
    echo sprintf("%s notification #%d: %s\n", $item['channel'], $item['id'], $ok ? 'sent' : 'failed') ;
}

echo 'Processed ' . count($items) . " notification(s).\n";
