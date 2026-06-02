<?php

require_admin();

$users = User::all([
    'search' => $_GET['search'] ?? '',
    'role' => $_GET['role'] ?? '',
]);

$title = 'Users';
require BASE_PATH . '/views/users/index.php';
