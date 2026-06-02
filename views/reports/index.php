<?php
/** @var string $dateFrom */
/** @var string $dateTo */
/** @var string|int $courtId */
/** @var list<array<string, mixed>> $courts */
/** @var array<string, int> $stats */
/** @var list<array<string, mixed>> $queues */
require BASE_PATH . '/views/layouts/header.php';
?>
<form class="row g-2 mb-3 align-items-end" method="GET">
    <div class="col-md-2">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dateFrom) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dateTo) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label small mb-1">Court</label>
        <select name="court_id" class="form-select form-select-sm">
            <option value="">All courts</option>
            <?php foreach ($courts as $c): ?>
                <option value="<?= $c['id'] ?>" <?= (string) $courtId === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['court_number']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-5 d-flex gap-2 flex-wrap">
        <button class="btn btn-primary btn-sm">Filter</button>
        <a href="<?= url('reports/export?date_from=' . urlencode($dateFrom) . '&date_to=' . urlencode($dateTo) . '&court_id=' . urlencode((string) $courtId)) ?>" class="btn btn-success btn-sm">Export CSV</a>
    </div>
</form>
<p class="text-muted small mb-3">
    Total: <strong><?= (int) $stats['total'] ?></strong>
    · Completed: <strong><?= (int) $stats['completed'] ?></strong>
    · Serving: <strong><?= (int) $stats['serving'] ?></strong>
    · Waiting: <strong><?= (int) $stats['waiting'] ?></strong>
    · Cancelled: <strong><?= (int) $stats['cancelled'] ?></strong>
</p>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead>
                <tr>
                    <th>Queue</th>
                    <th>Court</th>
                    <th>Match</th>
                    <th>Status</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Duration</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($queues)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No records for this date range.</td></tr>
            <?php endif; ?>
            <?php foreach ($queues as $q): ?>
                <tr>
                    <td><span class="badge bg-dark"><?= e($q['queue_number']) ?></span></td>
                    <td><?= e($q['court_number']) ?></td>
                    <td class="match-cell"><?php $queue = $q; $inline = true; require BASE_PATH . '/views/partials/queue_match.php'; unset($inline); ?></td>
                    <td><span class="badge bg-<?= status_badge($q['status']) ?>"><?= e(ucfirst($q['status'])) ?></span></td>
                    <td class="text-nowrap small"><?= format_datetime($q['served_at'] ?? null) ?></td>
                    <td class="text-nowrap small"><?= format_datetime($q['completed_at'] ?? null) ?></td>
                    <td class="text-nowrap small fw-semibold"><?= e(queue_play_duration_label($q)) ?></td>
                    <td class="text-nowrap small text-muted"><?= format_datetime($q['created_at'] ?? null) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
