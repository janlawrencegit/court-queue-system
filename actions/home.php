<?php

if (auth_check()) {
    redirect('dashboard');
}

$title = 'Welcome';
require BASE_PATH . '/views/home/index.php';
