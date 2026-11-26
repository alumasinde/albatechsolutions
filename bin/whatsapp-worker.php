<?php

declare(strict_types=1);

use AlbaTech\Core\Database;
use AlbaTech\Core\Env;
use AlbaTech\Repositories\SiteSettingsRepository;
use AlbaTech\Repositories\WhatsAppRepository;
use AlbaTech\Services\WhatsAppInbound;
use AlbaTech\Services\WhatsAppMessenger;

/**
 * WhatsApp background worker. Run every minute from cron:
 *
 *   * * * * * /usr/local/bin/php /home/USER/albatechsolutions/bin/whatsapp-worker.php >/dev/null 2>&1
 *
 * 1. Processes webhook events that were not finished inline (or failed) — up to 3 attempts.
 * 2. Retries queued outbound messages whose backoff has expired (up to 5 attempts).
 * 3. Applies retention: clears raw payloads and deletes expired customer files.
 *
 * Options: --quiet (no output)
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
Env::load($root . '/.env');
$quiet = in_array('--quiet', $argv ?? [], true);
$say = static function (string $line) use ($quiet): void {
    if (!$quiet) {
        echo $line . "\n";
    }
};

$pdo = Database::connection();
$lock = $pdo->query("SELECT GET_LOCK('albatech_whatsapp_worker', 0)")->fetchColumn();
if ((int) $lock !== 1) {
    $say('Another worker is running.');
    exit(0);
}

try {
    $repo = new WhatsAppRepository();
    $settings = (new SiteSettingsRepository())->publicValues();
    $inbound = new WhatsAppInbound($repo, new WhatsAppMessenger($repo), $settings);

    $repo->releaseStuckEvents();
    $events = $repo->eventsToProcess(50);
    foreach ($events as $event) {
        $inbound->processEvent((int) $event['id']);
    }
    $say('Events processed: ' . count($events));

    $say('Retries attempted: ' . (new WhatsAppMessenger($repo))->retryDue(20));

    $purged = $repo->purge((int) ($settings['whatsapp_payload_retention_days'] ?? 30), (int) ($settings['whatsapp_media_retention_days'] ?? 30));
    $mediaRoot = realpath(WhatsAppInbound::storagePath('whatsapp-media'));
    $deleted = 0;
    foreach ($purged['media'] as $relative) {
        $path = realpath(WhatsAppInbound::storagePath($relative));
        if ($path !== false && $mediaRoot !== false && str_starts_with($path, $mediaRoot . DIRECTORY_SEPARATOR) && @unlink($path)) {
            $deleted++;
        }
    }
    $say("Retention: {$purged['payloads']} payloads cleared, {$purged['events']} events deleted, {$deleted} files deleted.");
} catch (Throwable $e) {
    error_log('[WhatsApp worker] ' . $e->getMessage());
    $say('Worker error: ' . $e->getMessage());
    exit(1);
} finally {
    $pdo->query("SELECT RELEASE_LOCK('albatech_whatsapp_worker')");
}
