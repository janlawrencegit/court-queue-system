<?php

return [
    'app_name' => env('APP_NAME', 'Court Queue System'),
    'app_url' => env('APP_URL', 'https://pickleball.synergize.co'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Manila'),

    'db' => [
        'host' => env('DB_HOST', 'sql302.infinityfree.com'),
        'port' => env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'if0_39954650_ccp'),
        'user' => env('DB_USER', 'if0_39954650'),
        'pass' => env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
];
