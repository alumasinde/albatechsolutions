<?php

declare(strict_types=1);

use AlbaTech\Core\Env;
use AlbaTech\Core\WhatsAppLink;
use AlbaTech\Core\WhatsAppPolicy;
use AlbaTech\Repositories\WhatsAppRepository;
use AlbaTech\Services\WhatsAppInbound;
use AlbaTech\Services\WhatsAppMessenger;

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
Env::load($root . '/.env');

$failures = 0;
$count = 0;
$assert = static function (bool $condition, string $label) use (&$failures, &$count): void {
    $count++;
    if (!$condition) {
        $failures++;
        echo "FAIL: {$label}\n";
    }
};

// ---- signatures -------------------------------------------------------------------------
$body = '{"object":"whatsapp_business_account"}';
$good = 'sha256=' . hash_hmac('sha256', $body, 'secret');
$assert(WhatsAppPolicy::signatureValid($body, 'secret', $good), 'Valid signature accepted');
$assert(!WhatsAppPolicy::signatureValid($body, 'secret', 'sha256=bad'), 'Wrong signature rejected');
$assert(!WhatsAppPolicy::signatureValid($body, '', $good), 'Empty app secret never validates');
$assert(!WhatsAppPolicy::signatureValid($body, 'secret', ''), 'Missing signature header rejected');
$webhook = (string) file_get_contents($root . '/app/Controllers/WebhookController.php');
$assert(str_contains($webhook, "Env::get('APP_ENV', 'production') === 'local'"), 'Unsigned bypass is limited to APP_ENV=local');
$assert(!str_contains($webhook, 'whatsapp-webhook.log'), 'Webhook no longer writes raw payloads to a log file');
$assert(str_contains($webhook, 'storeEvent') && str_contains($webhook, 'respondAndContinue'), 'Webhook stores the event and answers before processing');

// ---- statuses, retries, hours, source tags ----------------------------------------------
$assert(WhatsAppPolicy::statusMayReplace('sent', 'delivered'), 'delivered replaces sent');
$assert(!WhatsAppPolicy::statusMayReplace('read', 'delivered'), 'read is never downgraded to delivered');
$assert(WhatsAppPolicy::statusMayReplace('sent', 'failed'), 'failed replaces sent');
$assert(!WhatsAppPolicy::statusMayReplace('read', 'failed'), 'failed never replaces read');
$assert(WhatsAppPolicy::isRetryable(429, '') && WhatsAppPolicy::isRetryable(503, '') && WhatsAppPolicy::isRetryable(400, '131056'), 'Rate limits and server errors retry');
$assert(!WhatsAppPolicy::isRetryable(400, '131047') && !WhatsAppPolicy::isRetryable(400, '132001'), 'Window-closed and template errors do not retry');
$assert(WhatsAppPolicy::backoffSeconds(1) < WhatsAppPolicy::backoffSeconds(3), 'Backoff grows');
$tz = new DateTimeZone('Africa/Nairobi');
$spec = 'Mon-Fri 08:00-18:00; Sat 09:00-13:00';
$assert(WhatsAppPolicy::isOpen($spec, new DateTimeImmutable('2026-11-25 10:00', $tz)), 'Open on a weekday morning');
$assert(!WhatsAppPolicy::isOpen($spec, new DateTimeImmutable('2026-11-25 19:30', $tz)), 'Closed on a weekday evening');
$assert(WhatsAppPolicy::isOpen($spec, new DateTimeImmutable('2026-11-28 12:59', $tz)) && !WhatsAppPolicy::isOpen($spec, new DateTimeImmutable('2026-11-28 13:00', $tz)), 'Saturday hours end at 13:00');
$assert(!WhatsAppPolicy::isOpen($spec, new DateTimeImmutable('2026-11-29 10:00', $tz)), 'Closed on Sunday');
$assert(WhatsAppPolicy::isOpen('', new DateTimeImmutable('2026-11-29 03:00', $tz)), 'Empty hours means always open');
$assert(WhatsAppPolicy::parseHours('nonsense') === [], 'Invalid hours parse to no rules');
$link = WhatsAppLink::build(['whatsapp_number' => '254712345678', 'whatsapp_prefill_message' => 'Hi'], '/get-help', '/services/kra-returns-filing');
$assert(WhatsAppPolicy::extractSourcePage(rawurldecode(explode('?text=', $link)[1])) === '/services/kra-returns-filing', 'Source page survives the wa.me round trip');
$assert(!str_contains(WhatsAppLink::build(['whatsapp_number' => '254712345678'], '/get-help', '/'), 'From'), 'Home page adds no source tag');
$assert(WhatsAppPolicy::isOptOut('STOP') && WhatsAppPolicy::isOptOut('  acha') && !WhatsAppPolicy::isOptOut('please do not stop'), 'Opt-out keywords match only at the start');
$assert((WhatsAppPolicy::keywordTopic('I need help with my KRA returns')['path'] ?? '') === '/services/kra-ecitizen-help', 'KRA questions go to the compliance hub');
$assert((WhatsAppPolicy::keywordTopic('Do you build POS systems?')['path'] ?? '') === '/solutions', 'System questions go to solutions');
$assert(WhatsAppPolicy::keywordTopic('what time is it') === null, 'Generic words like "time" no longer trigger replies');

// ---- database behaviour (API disabled, nothing is sent) ---------------------------------
// Production MySQL/MariaDB runs with ONLY_FULL_GROUP_BY; local XAMPP does not. Test like production.
\AlbaTech\Core\Database::connection()->exec("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
$repo = new WhatsAppRepository();
$number = '254700999001';
$repo->deleteConversation($number);
$repo->optIn($number);
$settings = ['whatsapp_api_enabled' => '0', 'whatsapp_email_alerts' => '0', 'site_name' => 'AlbaTech Solutions'];
$inbound = new WhatsAppInbound($repo, new WhatsAppMessenger($repo), $settings);
$payload = ['object' => 'whatsapp_business_account', 'entry' => [['changes' => [['value' => [
    'contacts' => [['wa_id' => $number, 'profile' => ['name' => 'Test Person']]],
    'messages' => [['from' => $number, 'id' => 'wamid.TEST-HARDENING-1', 'type' => 'text', 'text' => ['body' => "Hi\n\n(From: /services/sme-launchpad)"]]],
]]]]]];
$inbound->process($payload);
$inbound->process($payload); // Meta retry
$thread = $repo->thread($number);
$assert(count($thread) === 1, 'Duplicate webhook delivery stores the message once');
$contact = $repo->contact($number) ?? [];
$assert(!empty($contact['window_open']), 'Inbound message opens the 24-hour window');
$assert(($contact['source_page'] ?? '') === '/services/sme-launchpad', 'Source page recorded on the contact');
$assert(($contact['contact_name'] ?? '') === 'Test Person', 'Contact name recorded');
$inboxRow = null;
try {
    foreach ($repo->conversations() as $row) {
        if ($row['wa_number'] === $number) { $inboxRow = $row; }
    }
    $repo->health();
    $repo->optOuts();
} catch (Throwable $e) {
    echo 'Inbox query error: ' . $e->getMessage() . "\n";
}
$assert($inboxRow !== null && (int) $inboxRow['total'] === 1 && $inboxRow['contact_name'] === 'Test Person' && !empty($inboxRow['window_open']), 'Inbox list works under ONLY_FULL_GROUP_BY');

$outId = $repo->recordMessage(['direction' => 'out', 'origin' => 'agent', 'wa_number' => $number, 'message_type' => 'text', 'body' => 'Reply', 'wa_message_id' => 'wamid.TEST-HARDENING-OUT', 'status' => 'sent']);
$repo->markStatus('wamid.TEST-HARDENING-OUT', 'read');
$repo->markStatus('wamid.TEST-HARDENING-OUT', 'delivered');
$assert(($repo->message((int) $outId)['status'] ?? '') === 'read', 'Out-of-order delivered status does not overwrite read');

$repo->pauseBot($number, 24);
$assert(!empty(($repo->contact($number) ?? [])['bot_paused']), 'Agent takeover pauses automation');
$repo->resumeBot($number);
$assert(empty(($repo->contact($number) ?? [])['bot_paused']), 'Automation can be resumed');

$repo->optOut($number);
$assert($repo->isOptedOut($number), 'Opt-out recorded');
$repo->deleteConversation($number);
$assert($repo->thread($number) === [] && $repo->contact($number) === null, 'Conversation deletion removes messages and contact');
$assert($repo->isOptedOut($number), 'Opt-out survives conversation deletion');
$repo->optIn($number);

$result = (new WhatsAppMessenger($repo))->sendText($number, 'not configured test', 'system');
$assert(!$result['ok'] && !$result['queued'], 'Missing credentials fail fast and are not queued for retry');
$repo->deleteConversation($number);

$thread = (string) file_get_contents($root . '/app/Repositories/WhatsAppRepository.php');
$assert(str_contains($thread, 'ORDER BY created_at DESC, id DESC LIMIT :lim'), 'Inbox thread loads the newest messages');

if ($failures > 0) {
    echo "{$failures} of {$count} WhatsApp hardening checks failed.\n";
    exit(1);
}
echo "PASS: {$count} WhatsApp hardening checks.\n";
