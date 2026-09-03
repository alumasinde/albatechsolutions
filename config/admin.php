<?php

return [
    // Change in .env to move the admin panel off a guessable path.
    'path' => rtrim($_ENV['ADMIN_PATH'] ?? '/admin', '/'),
];
