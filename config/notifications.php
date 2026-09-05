<?php

return [
    'retry' => [
        'max_attempts' => max(1, (int)($_ENV['NOTIFICATION_MAX_ATTEMPTS'] ?? 3)),
        'base_delay_minutes' => max(1, (int)($_ENV['NOTIFICATION_RETRY_BASE_MINUTES'] ?? 5)),
    ],
    'channels' => [
        'email' => filter_var($_ENV['MAIL_HOST'] ?? '', FILTER_VALIDATE_BOOL) || !empty($_ENV['MAIL_HOST'] ?? null),
        'sms' => filter_var($_ENV['SMS_ENABLED'] ?? false, FILTER_VALIDATE_BOOL),
        'whatsapp' => filter_var($_ENV['WHATSAPP_ENABLED'] ?? false, FILTER_VALIDATE_BOOL)
            && in_array(strtolower((string)($_ENV['WHATSAPP_PROVIDER'] ?? 'meta')), ['meta', 'callmebot'], true),
    ],
];
