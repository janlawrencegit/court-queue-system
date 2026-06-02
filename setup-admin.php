<?php

/**
 * ONE-TIME SETUP — Reset admin login
 * 1. Upload to htdocs
 * 2. Open: https://pickleball.synergize.co/setup-admin.php?key=courtsetup2026
 * 3. DELETE this file immediately after success
 */

declare(strict_types=1);

$secret = 'courtsetup2026';
if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Forbidden. Use: setup-admin.php?key=courtsetup2026');
}

require __DIR__ . '/includes/init.php';

$email = 'admin@courtqueue.com';
$plainPassword = 'password';
$name = 'Admin User';

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::get();
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

    $existing = User::findByEmail($email);

    if ($existing) {
        $stmt = $db->prepare(
            'UPDATE users SET name = ?, password = ?, role = ?, is_active = 1, deleted_at = NULL, updated_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$name, $hash, 'admin', $existing['id']]);
        $action = 'updated';
    } else {
        $stmt = $db->prepare(
            'INSERT INTO users (name, email, email_verified_at, password, role, phone, is_active, created_at, updated_at)
             VALUES (?, ?, NOW(), ?, ?, ?, 1, NOW(), NOW())'
        );
        $stmt->execute([$name, $email, $hash, 'admin', '123-456-7890']);
        $action = 'created';
    }

    $verify = User::findByEmail($email);
    $ok = $verify && password_verify($plainPassword, $verify['password']);

    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Setup OK</title>';
    echo '<style>body{font-family:system-ui;max-width:520px;margin:40px auto;padding:20px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px}</style></head><body>';
    echo '<h2>Admin account ' . htmlspecialchars($action) . '</h2>';
    echo '<p><strong>Login URL:</strong> <a href="' . htmlspecialchars(url('login')) . '">' . htmlspecialchars(url('login')) . '</a></p>';
    echo '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '<br>';
    echo '<strong>Password:</strong> ' . htmlspecialchars($plainPassword) . '</p>';
    echo '<p>Password test on server: <strong>' . ($ok ? 'PASSED' : 'FAILED') . '</strong></p>';
    echo '<p style="color:#b45309"><strong>DELETE setup-admin.php now</strong> from your server for security.</p>';
    echo '</body></html>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h2>Setup failed</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>Fix database connection in .env first.</p>';
}
