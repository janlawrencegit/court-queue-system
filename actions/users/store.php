<?php

require_admin();
csrf_verify();

User::create([
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'password' => $_POST['password'] ?? '',
    'role' => $_POST['role'] ?? 'staff',
    'phone' => $_POST['phone'] ?? '',
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
]);

flash('success', 'User created.');
redirect('users');
