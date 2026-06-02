<?php

if (auth_check()) {
    redirect('dashboard');
}

$title = 'Login';
require BASE_PATH . '/views/auth/login.php';
