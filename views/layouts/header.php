<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <?php favicon_tags(); ?>
    <title><?= e($title ?? 'Court Queue') ?> - <?= e(app_name()) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body data-csrf-token="<?= e(csrf_token()) ?>" data-player-search-url="<?= url('players/search') ?>">
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<nav class="sidebar" id="sidebar">
    <div class="brand">
        <h4><i class="fas fa-gavel me-2"></i><?= e(app_name()) ?></h4>
        <?php if ($orgName = setting('organization_name')): ?>
        <small><?= e($orgName) ?></small>
        <?php endif; ?>
    </div>
    <div class="nav-menu">
        <div class="nav-section">Main</div>
        <a href="<?= url('dashboard') ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <div class="nav-section">Management</div>
        <a href="<?= url('courts') ?>" class="nav-link"><i class="fas fa-building"></i> Courts</a>
        <a href="<?= url('queues') ?>" class="nav-link"><i class="fas fa-users"></i> Queues</a>
        <a href="<?= url('players') ?>" class="nav-link"><i class="fas fa-user-friends"></i> Players</a>
        <div class="nav-section">Display</div>
        <a href="<?= url('display') ?>" class="nav-link" target="_blank"><i class="fas fa-tv"></i> Public Display</a>
        <a href="<?= url('reports') ?>" class="nav-link"><i class="fas fa-chart-bar"></i> Reports</a>
        <?php if (is_admin()): ?>
        <div class="nav-section">Admin</div>
        <a href="<?= url('users') ?>" class="nav-link"><i class="fas fa-user-cog"></i> Users</a>
        <a href="<?= url('settings') ?>" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        <?php endif; ?>
    </div>
</nav>
<div class="main-content">
    <header class="top-header">
        <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <h5 class="mb-0 fw-semibold"><?= e($title ?? '') ?></h5>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <?php $currentUser = auth_user(); ?>
                <div class="avatar"><?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?></div>
                <span class="ms-2 d-none d-md-inline text-dark"><?= e($currentUser['name'] ?? '') ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-muted"><?= e(ucfirst($_SESSION['user_role'] ?? '')) ?></span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
    </header>
    <div class="content-area">
<?php $flash = get_flash();
if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
<?php endif; ?>
