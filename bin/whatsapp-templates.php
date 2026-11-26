<?php

declare(strict_types=1);

use AlbaTech\Core\Env;

/**
 * Lists message templates on the configured WhatsApp Business Account,
 * including declared components/variables — for diagnosing template send
 * errors (#132001 name/language mismatch, #100 parameter problems).
 *
 * Usage: php bin/whatsapp-templates.php [name-filter]
 */
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
Env::load($root . '/.env');

$wabaId = trim((string) Env::get('WHATSAPP_BUSINESS_ACCOUNT_ID', ''));
$token = trim((string) Env::get('WHATSAPP_ACCESS_TOKEN', ''));
if ($wabaId === '' || $token === '') {
    fwrite(STDERR, "Set WHATSAPP_BUSINESS_ACCOUNT_ID and WHATSAPP_ACCESS_TOKEN in .env first.\n");
    exit(1);
}

$filter = strtolower((string) ($argv[1] ?? ''));
$url = 'https://graph.facebook.com/v22.0/' . rawurlencode($wabaId)
    . '/message_templates?fields=name,language,status,category,components&limit=100';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
    CURLOPT_TIMEOUT => 20,
]);
$raw = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
if ($raw === false) {
    fwrite(STDERR, 'Request failed: ' . curl_error($ch) . "\n");
    curl_close($ch);
    exit(1);
}
curl_close($ch);

$data = json_decode((string) $raw, true);
if ($status < 200 || $status >= 300 || !is_array($data)) {
    fwrite(STDERR, 'HTTP ' . $status . ': ' . substr((string) $raw, 0, 1000) . "\n");
    exit(1);
}

$found = 0;
foreach ((array) ($data['data'] ?? []) as $template) {
    $name = (string) ($template['name'] ?? '');
    if ($filter !== '' && !str_contains(strtolower($name), $filter)) {
        continue;
    }
    $found++;
    echo $name . '  [' . (string) ($template['status'] ?? '?') . ']  lang=' . (string) ($template['language'] ?? '?') . '  cat=' . (string) ($template['category'] ?? '?') . "\n";
    foreach ((array) ($template['components'] ?? []) as $component) {
        $type = (string) ($component['type'] ?? '?');
        $line = '  ' . $type;
        if (isset($component['format'])) {
            $line .= ' (' . (string) $component['format'] . ')';
        }
        if (isset($component['text'])) {
            $line .= ': ' . mb_substr((string) $component['text'], 0, 200);
        }
        echo $line . "\n";
        $named = (array) ($component['example']['body_text_named_params'] ?? []);
        if ($named !== []) {
            foreach ($named as $param) {
                echo '    var: ' . (string) ($param['param_name'] ?? '?') . "\n";
            }
        }
        $positional = (array) ($component['example']['body_text'][0] ?? []);
        if ($positional !== []) {
            echo '    positional vars: ' . count($positional) . "\n";
        }
    }
    echo "\n";
}
echo $found . " template(s) found.\n";
