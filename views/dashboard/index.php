<?php require BASE_PATH . '/views/layouts/header.php'; ?>

<?php if (!empty($dbError)): ?>
<div class="alert alert-danger">
    <strong>Dashboard error:</strong> <?= e($dbError) ?>
    <br><small>Import <code>database.sql</code> in phpMyAdmin if tables are missing.</small>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Total Courts</div><div class="stat-value"><?= (int)$stats['total_courts'] ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Active Courts</div><div class="stat-value text-success"><?= (int)$stats['active_courts'] ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Waiting</div><div class="stat-value text-warning"><?= (int)$stats['players_waiting'] ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Completed Today</div><div class="stat-value text-info"><?= (int)$stats['completed_today'] ?></div></div></div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <span>Court Status</span>
        <a href="<?= url('courts') ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Court</th><th>Status</th><th>Now Serving</th><th>Next</th><th>Waiting</th></tr></thead>
            <tbody>
            <?php foreach ($courts as $c): ?>
            <tr>
                <td><strong><?= e($c['court_number']) ?></strong></td>
                <td><span class="badge bg-<?= status_badge($c['status']) ?>"><?= e(ucfirst($c['status'])) ?></span></td>
                <td><?= $c['current_queue'] ? e($c['current_queue']['queue_number']) : '-' ?></td>
                <td><?= $c['next_queue'] ? e($c['next_queue']['queue_number']) : '-' ?></td>
                <td><span class="badge bg-warning text-dark"><?= (int)$c['waiting_count'] ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex">
    <a href="<?= url('queues/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i>New Queue</a>
    <a href="<?= url('display') ?>" class="btn btn-outline-dark" target="_blank"><i class="fas fa-tv me-2"></i>Display Screen</a>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
