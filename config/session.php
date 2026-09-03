<?php

return [
    'name'     => $_ENV['SESSION_NAME'] ?? 'albatech_session',
    'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120), // minutes
    'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'httponly' => true,
    'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Lax',
    'strict_mode' => true,
    'use_only_cookies' => true,
    'use_trans_sid' => false,
];
