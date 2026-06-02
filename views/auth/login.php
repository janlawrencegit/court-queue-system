<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php favicon_tags(); ?>
    <title>Login - <?= e(app_name()) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #0f172a, #1e40af); display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 400px; width: 100%; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,.25); }
        .login-header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: #fff; padding: 32px; text-align: center; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <i class="fas fa-gavel fa-2x mb-2"></i>
        <h4 class="fw-bold mb-0"><?= e(app_name()) ?></h4>
    </div>
    <div class="p-4">
        <?php $flash = get_flash(); if ($flash): ?>
        <div class="alert alert-danger"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <p class="text-center mb-3"><a href="<?= url('/') ?>" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to home</a></p>
        <form method="POST" action="<?= url('login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
    </div>
</div>
</body>
</html>
