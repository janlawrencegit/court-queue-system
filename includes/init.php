<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/env.php';

define('BASE_PATH', dirname(__DIR__));

load_env_file(BASE_PATH . '/.env');

define('BASE_URL', rtrim((string) env('APP_URL', ''), '/'));

if (env('APP_DEBUG', '0') === '1' || env('APP_DEBUG', '') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

set_exception_handler(function (Throwable $e) {
    http_response_code(500);
    if (env('APP_DEBUG', '0') === '1' || env('APP_DEBUG', '') === 'true') {
        echo '<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . "\n\n";
        echo htmlspecialchars($e->getFile() . ':' . $e->getLine()) . '</pre>';
    } else {
        echo '<h1>Something went wrong</h1><p>Enable APP_DEBUG=1 in .env or open health.php for details.</p>';
    }
    exit;
});

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Manila'));

$config = require BASE_PATH . '/config/config.php';
date_default_timezone_set($config['timezone']);

require_once BASE_PATH . '/includes/helpers.php';
require_once BASE_PATH . '/includes/csrf.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/models/Database.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Court.php';
require_once BASE_PATH . '/models/Player.php';
require_once BASE_PATH . '/models/Queue.php';
