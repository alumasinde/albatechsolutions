<?php

declare(strict_types=1);

return [
    'base_url'       => $_ENV['PAYSTACK_BASE_URL'] ?? 'https://api.paystack.co',
    'public_key'     => $_ENV['PAYSTACK_PUBLIC_KEY'] ?? '',
    'secret_key'     => $_ENV['PAYSTACK_SECRET_KEY'] ?? '',
    'callback_url'   => $_ENV['PAYSTACK_CALLBACK_URL'] ?? '',
    'currency'       => $_ENV['PAYSTACK_CURRENCY'] ?? 'KES',
];
