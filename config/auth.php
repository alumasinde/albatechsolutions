<?php

return [
    // Change this in .env to move the login form off the guessable
    // default path (e.g. LOGIN_PATH=/staff-portal-access).
    'login_path' => $_ENV['LOGIN_PATH'] ?? '/login',
];
