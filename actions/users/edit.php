<?php

require_admin();

$id = (int) ($_GET['id'] ?? 0);
$user = User::findById($id);
if (!$user) {
    flash('error', 'User not found.');
    redirect('users');
}

$title = 'Edit User';
require BASE_PATH . '/views/users/edit.php';
