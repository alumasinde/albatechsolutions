<?php

declare(strict_types=1);

return [
    'dsn' => trim((string) ($_ENV['SENTRY_DSN'] ?? '')),
    'environment' => $_ENV['SENTRY_ENVIRONMENT'] ?? ($_ENV['APP_ENV'] ?? 'production'),
    'release' => trim((string) ($_ENV['SENTRY_RELEASE'] ?? '')),
    'traces_sample_rate' => max(0.0, min(1.0, (float) ($_ENV['SENTRY_TRACES_SAMPLE_RATE'] ?? 0))),
    'send_default_pii' => filter_var($_ENV['SENTRY_SEND_DEFAULT_PII'] ?? false, FILTER_VALIDATE_BOOLEAN),
];
