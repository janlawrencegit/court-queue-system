<?php

csrf_verify();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    flash('error', 'Email and password are required.');
    set_old($_POST);
    redirect('login');
}

$user = User::findByEmail($email);

if (!$user || !password_verify($password, $user['password'])) {
    flash('error', 'Invalid credentials.');
    set_old($_POST);
    redirect('login');
}

if (!(int) $user['is_active']) {
    flash('error', 'Your account is inactive.');
    redirect('login');
}

auth_login($user);
clear_old();
redirect('dashboard');
