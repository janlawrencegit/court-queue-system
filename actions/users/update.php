<?php

require_admin();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$data = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'role' => $_POST['role'] ?? 'staff',
    'phone' => $_POST['phone'] ?? '',
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
];
if (!empty($_POST['password'])) {
    $data['password'] = $_POST['password'];
}
User::update($id, $data);

flash('success', 'User updated.');
redirect('users');
