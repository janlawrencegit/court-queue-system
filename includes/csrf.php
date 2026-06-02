<?php

declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if ($token === '' || !hash_equals(csrf_token(), (string) $token)) {
        if (is_post() && !empty($_POST['redirect'])) {
            flash('error', 'Session expired. Please try again.');
            redirect((string) $_POST['redirect']);
        }
        http_response_code(419);
        die('Invalid CSRF token.');
    }
}
