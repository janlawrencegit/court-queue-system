<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// Strip subdirectory if app is not at web root
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
if ($scriptDir && $scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir)) ?: '/';
}

$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        '/' => 'actions/home.php',
        '/login' => 'actions/auth/login.php',
        '/logout' => 'actions/auth/logout.php',
        '/dashboard' => 'actions/dashboard/index.php',
        '/courts' => 'actions/courts/index.php',
        '/courts/create' => 'actions/courts/create.php',
        '/courts/show' => 'actions/courts/show.php',
        '/courts/edit' => 'actions/courts/edit.php',
        '/players' => 'actions/players/index.php',
        '/players/create' => 'actions/players/create.php',
        '/players/show' => 'actions/players/show.php',
        '/players/edit' => 'actions/players/edit.php',
        '/players/search' => 'actions/players/search.php',
        '/queues' => 'actions/queues/index.php',
        '/queues/create' => 'actions/queues/create.php',
        '/queues/show' => 'actions/queues/show.php',
        '/queues/edit' => 'actions/queues/edit.php',
        '/display' => 'actions/display/index.php',
        '/display/single' => 'actions/display/single.php',
        '/display/data' => 'actions/display/data.php',
        '/reports' => 'actions/reports/index.php',
        '/reports/export' => 'actions/reports/export.php',
        '/users' => 'actions/users/index.php',
        '/users/create' => 'actions/users/create.php',
        '/users/edit' => 'actions/users/edit.php',
        '/settings' => 'actions/settings/index.php',
    ],
    'POST' => [
        '/login' => 'actions/auth/login_post.php',
        '/courts/store' => 'actions/courts/store.php',
        '/courts/update' => 'actions/courts/update.php',
        '/courts/delete' => 'actions/courts/delete.php',
        '/courts/status' => 'actions/courts/status.php',
        '/players/store' => 'actions/players/store.php',
        '/players/update' => 'actions/players/update.php',
        '/players/delete' => 'actions/players/delete.php',
        '/queues/store' => 'actions/queues/store.php',
        '/queues/update' => 'actions/queues/update.php',
        '/queues/delete' => 'actions/queues/delete.php',
        '/queues/action' => 'actions/queues/action.php',
        '/users/store' => 'actions/users/store.php',
        '/users/update' => 'actions/users/update.php',
        '/users/delete' => 'actions/users/delete.php',
        '/users/toggle' => 'actions/users/toggle.php',
        '/settings/save' => 'actions/settings/save.php',
    ],
];

$file = $routes[$method][$uri] ?? null;

if (!$file || !is_file(BASE_PATH . '/' . $file)) {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

require_once BASE_PATH . '/' . $file;
