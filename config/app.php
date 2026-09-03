<?php

return [
    'name'  => $_ENV['APP_NAME'] ?? 'AlbaTech Solutions',
    'env'   => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url'   => rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'),
    'key'   => $_ENV['APP_KEY'] ?? '',
    'timezone' => 'Africa/Nairobi',
    // Comma-separated IPs can be supplied through TRUSTED_PROXIES when
    // the app sits behind a known reverse proxy/load balancer.
    'trusted_proxies' => array_values(array_filter(array_map('trim', explode(',', (string) ($_ENV['TRUSTED_PROXIES'] ?? ''))))),
    'maintenance' => filter_var($_ENV['MAINTENANCE_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'release' => $_ENV['APP_RELEASE'] ?? 'v4.0',
];
