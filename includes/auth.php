<?php

declare(strict_types=1);

function auth_check(): bool
{
    return isset($_SESSION['user_id']);
}

function auth_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function auth_user(): ?array
{
    if (!auth_check()) {
        return null;
    }
    static $user;
    if ($user === null) {
        $user = User::findById((int) $_SESSION['user_id']);
    }
    return $user;
}

function auth_login(array $user): void
{
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_auth(): void
{
    if (!auth_check()) {
        redirect('login');
    }
    $user = auth_user();
    if (!$user || !(int) $user['is_active']) {
        auth_logout();
        flash('error', 'Your account is inactive.');
        redirect('login');
    }
}

function require_admin(): void
{
    require_auth();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        die('Admin access required.');
    }
}

function require_roles(array $roles): void
{
    require_auth();
    if (!in_array($_SESSION['user_role'] ?? '', $roles, true)) {
        http_response_code(403);
        die('Unauthorized.');
    }
}

function is_admin(): bool
{
    return ($_SESSION['user_role'] ?? '') === 'admin';
}
