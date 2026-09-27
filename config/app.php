<?php

return [
    'name' => 'Clinic Management System',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost/clinic-management/public',
    'timezone' => 'UTC',
    'locale' => 'en',
    'base_url' => $_ENV['APP_URL'] ?? 'http://localhost/clinic-management/public',
    'csrf_secret' => $_ENV['CSRF_SECRET'] ?? 'default-secret-change-me',
    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120),
        'secure' => ($_ENV['SESSION_SECURE'] ?? 'false') === 'true',
        'httponly' => ($_ENV['SESSION_HTTPONLY'] ?? 'true') === 'true',
        'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'lax',
    ],
];