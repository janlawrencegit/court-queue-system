<?php
/** @var array<string, mixed> $queue */
/** @var list<array<string, mixed>> $logs */
require BASE_PATH . '/views/layouts/header.php';
?>
<script>document.body.dataset.queueActionUrl = '<?= url('queues/action') ?>';</script>
<div class="row g-3 mb-3 queue-show-page">
    <div class="col-lg-8">
        <div class="card queue-show-main">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-ticket-alt me-2"></i><?= e($queue['queue_number']) ?></span>
                <span class="badge bg-<?= status_badge($queue['status']) ?>"><?= e(ucfirst($queue['status'])) ?></span>
            </div>
            <div class="card-body">
                <div class="queue-show-players mb-3 p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase small mb-0">Players</h6>
                    </div>
                    <?php $allPlayers = array_values(array_filter(array_merge(queue_team_members($queue, 1), queue_team_members($queue, 2)))); ?>
                    <div class="queue-show-player-list">
                        <?php foreach ($allPlayers as $idx => $name): ?>
                            <div class="queue-show-player-chip">
                                <span class="queue-show-player-order"><?= (int) $idx + 1 ?></span>
                                <span class="queue-show-player-name"><?= e((string) $name) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="row g-2 queue-show-metrics">
                    <div class="col-6 col-md-4">
                        <div class="queue-metric-card">
                            <h6 class="text-muted text-uppercase small mb-1">Court</h6>
                            <p class="mb-0 fw-semibold"><?= e($queue['court_number']) ?></p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="queue-metric-card">
                            <h6 class="text-muted text-uppercase small mb-1">Start time</h6>
                            <p class="mb-0"><?= format_datetime($queue['served_at'] ?? null) ?></p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="queue-metric-card">
                            <h6 class="text-muted text-uppercase small mb-1">Play duration</h6>
                            <p class="mb-0">
                                <?php if (!empty($queue['served_at'])): ?>
                                    <span class="badge bg-warning text-dark"><?= e(queue_play_duration_label($queue)) ?></span>
                                    <?php if (queue_overtime_minutes($queue) > 0): ?>
                                        <span class="badge bg-danger ms-1"><?= e(queue_overtime_label($queue)) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="queue-metric-card">
                            <h6 class="text-muted text-uppercase small mb-1">End time</h6>
                            <p class="mb-0">
                                <?= format_datetime(!empty($queue['completed_at']) ? $queue['completed_at'] : ($queue['rental_ends_at'] ?? null)) ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="queue-metric-card">
                            <h6 class="text-muted text-uppercase small mb-1">Queued at</h6>
                            <p class="mb-0"><?= format_datetime($queue['created_at'] ?? null) ?></p>
                        </div>
                    </div>
                </div>
                <?php if (!empty($queue['notes'])): ?>
                    <hr>
                    <p class="mb-0"><strong>Notes:</strong> <?= e($queue['notes']) ?></p>
                <?php endif; ?>
                <div class="btn-group mt-3">
                    <?php if (!in_array($queue['status'], ['completed', 'cancelled'], true)): ?>
                        <a href="<?= url('queues/edit?id=' . $queue['id']) ?>" class="btn btn-warning">Edit</a>
                    <?php endif; ?>
                    <?php if ($queue['status'] === 'waiting'): ?>
                        <button class="btn btn-success" data-queue-action="serve" data-id="<?= $queue['id'] ?>">Start (Serve)</button>
                    <?php endif; ?>
                    <?php if (in_array($queue['status'], ['waiting', 'called', 'serving'], true)): ?>
                        <button class="btn btn-info" data-queue-action="complete" data-id="<?= $queue['id'] ?>">End (Complete)</button>
                    <?php endif; ?>
                    <?php if ($queue['status'] === 'serving'): ?>
                        <button class="btn btn-danger" data-queue-action="extend" data-id="<?= $queue['id'] ?>">Extend Time</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 queue-show-log">
            <div class="card-header">Activity Log</div>
            <ul class="list-group list-group-flush queue-show-log-list">
                <?php if (empty($logs)): ?>
                    <li class="list-group-item text-muted">No activity yet.</li>
                <?php endif; ?>
                <?php foreach ($logs as $log): ?>
                    <li class="list-group-item">
                        <div class="fw-semibold text-capitalize"><?= e($log['action']) ?></div>
                        <div class="small text-muted">
                            <?= e($log['old_status'] ?? '—') ?> → <?= e($log['new_status'] ?? '—') ?>
                        </div>
                        <div class="small text-muted mt-1">
                            <?= e($log['performer_name'] ?? 'System') ?> · <?= format_datetime($log['created_at'] ?? null) ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<p class="mt-3"><a href="<?= url('queues') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Queues</a></p>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
