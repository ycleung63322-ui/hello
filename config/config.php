<?php

declare(strict_types=1);

/**
 * Application configuration.
 * Copy values from .env.example or edit directly for local development.
 */
return [
    'app_name' => 'MemberHub',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'member_system',
        'user' => getenv('DB_USER') ?: 'member_app',
        'pass' => getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : 'member_secret',
        'charset' => 'utf8mb4',
    ],
];
