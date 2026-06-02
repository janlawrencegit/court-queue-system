<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php favicon_tags(); ?>
    <title><?= e(app_name()) ?> — Court Queue Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --dark: #0f172a;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: var(--dark);
            color: #f1f5f9;
        }
        .navbar-landing {
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .brand {
            font-weight: 800;
            font-size: 1.25rem;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .hero {
            padding: 5rem 2rem 4rem;
            text-align: center;
            background: radial-gradient(ellipse at 50% 0%, rgba(59,130,246,.25) 0%, transparent 60%);
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.25rem);
            font-weight: 800;
            letter-spacing: -.02em;
            margin-bottom: 1rem;
        }
        .hero p.lead {
            font-size: 1.15rem;
            color: #94a3b8;
            max-width: 560px;
            margin: 0 auto 2.5rem;
        }
        .btn-hero {
            padding: .85rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            font-size: 1rem;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            color: #fff;
        }
        .btn-primary-custom:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(30,64,175,.35);
        }
        .btn-outline-custom {
            border: 2px solid rgba(255,255,255,.3);
            color: #fff;
            background: transparent;
        }
        .btn-outline-custom:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
            border-color: rgba(255,255,255,.5);
        }
        .features {
            padding: 4rem 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        .feature-card {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            transition: transform .2s, box-shadow .2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,.3);
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(59,130,246,.2);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }
        .feature-card p {
            color: #94a3b8;
            font-size: .9rem;
            margin: 0;
        }
        .footer-landing {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: .85rem;
            border-top: 1px solid rgba(255,255,255,.06);
        }
    </style>
</head>
<body>
    <nav class="navbar-landing">
        <a href="<?= url('/') ?>" class="brand">
            <i class="fas fa-gavel"></i> <?= e(app_name()) ?>
        </a>
        <div class="d-flex gap-2">
            <a href="<?= url('login') ?>" class="btn btn-primary-custom btn-sm btn-hero">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>
        </div>
    </nav>

    <section class="hero">
        <h1><?= e(app_name()) ?><br>Queuing System</h1>
        <p class="lead">
            Manage courts, players, and queues in real time. Perfect for sports facilities,
            government offices, and recreation centers.
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="<?= url('display') ?>" class="btn btn-primary-custom btn-hero" target="_blank">
                <i class="fas fa-tv me-2"></i>Live Display
            </a>
        </div>
    </section>

    <section class="features">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-building"></i></div>
                    <h3>Court Management</h3>
                    <p>Create and monitor multiple courts with live status — available, occupied, or closed.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Smart Queuing</h3>
                    <p>Automatic queue numbers, call and serve workflow, skip, recall, and complete tracking.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Reports & Display</h3>
                    <p>Public signage screen for waiting players plus daily reports and CSV export.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-landing">
        &copy; <?= date('Y') ?> <?= e(app_name()) ?>. All rights reserved.
    </footer>
</body>
</html>
