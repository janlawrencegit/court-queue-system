<?php
/** @var list<array<string, mixed>> $queues */
/** @var list<array<string, mixed>> $courts */
/** @var string|null $queueError */
require BASE_PATH . '/views/layouts/header.php';
?>
<script>document.body.dataset.queueActionUrl = '<?= url('queues/action') ?>';</script>
<?php if (!empty($queueError)): ?>
<div class="alert alert-danger">
    Queue error: <?= e($queueError) ?>
</div>
<?php endif; ?>
<div class="d-flex justify-content-between mb-3">
    <form class="row g-2 flex-grow-1" method="GET">
        <div class="col-md-3"><input name="search" class="form-control form-control-sm" placeholder="Search player or queue #" value="<?= e($_GET['search'] ?? '') ?>"></div>
        <div class="col-md-2"><select name="court_id" class="form-select form-select-sm"><option value="">All courts</option>
            <?php foreach ($courts as $c): ?><option value="<?= $c['id'] ?>" <?= ($_GET['court_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['court_number']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="col-md-2"><input type="date" name="date" class="form-control form-control-sm" value="<?= e($_GET['date'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-md-2"><button class="btn btn-sm btn-primary">Filter</button></div>
    </form>
    <a href="<?= url('queues/create') ?>" class="btn btn-primary btn-sm ms-2">New Match</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Queue #</th>
                    <th>Court</th>
                    <th>Match</th>
                    <th>Start</th>
                    <th>Duration</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($queues)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">
                    No queue entries for this date.
                    <a href="<?= url('queues?date=' . date('Y-m-d')) ?>" class="d-block small mt-1">Show today (<?= e(date('M j, Y')) ?>)</a>
                </td></tr>
            <?php endif; ?>
            <?php foreach ($queues as $q): ?>
                <tr>
                    <td><span class="badge bg-dark"><?= e($q['queue_number']) ?></span></td>
                    <td><?= e($q['court_number']) ?></td>
                    <td class="match-cell"><?php $queue = $q; $inline = true; require BASE_PATH . '/views/partials/queue_match.php'; unset($inline); ?></td>
                    <td class="text-nowrap"><?= format_datetime($q['served_at'] ?? null) ?></td>
                    <td class="text-nowrap">
                        <?php if (!empty($q['served_at'])): ?>
                            <span class="badge bg-dark-subtle text-dark-emphasis border"><?= e(queue_play_duration_label($q)) ?></span>
                            <?php if (queue_overtime_minutes($q) > 0): ?>
                                <div><span class="badge bg-danger mt-1"><?= e(queue_overtime_label($q)) ?></span></div>
                            <?php endif; ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <?= format_datetime(!empty($q['completed_at']) ? $q['completed_at'] : ($q['rental_ends_at'] ?? null)) ?>
                    </td>
                    <td><span class="badge bg-<?= status_badge($q['status']) ?>"><?= e(ucfirst($q['status'])) ?></span></td>
                    <td class="text-nowrap">
                        <?php if ($q['status'] === 'waiting'): ?>
                            <button class="btn btn-sm btn-outline-success" data-queue-action="serve" data-id="<?= $q['id'] ?>" title="Start"><i class="fas fa-play"></i></button>
                            <button class="btn btn-sm btn-outline-warning" data-queue-action="skip" data-id="<?= $q['id'] ?>" title="Skip"><i class="fas fa-forward"></i></button>
                        <?php endif; ?>
                        <?php if (in_array($q['status'], ['waiting', 'called', 'serving'], true)): ?>
                            <button class="btn btn-sm btn-outline-info" data-queue-action="complete" data-id="<?= $q['id'] ?>" title="Complete"><i class="fas fa-check"></i></button>
                        <?php endif; ?>
                        <?php if ($q['status'] === 'serving'): ?>
                            <button class="btn btn-sm btn-outline-danger" data-queue-action="extend" data-id="<?= $q['id'] ?>" title="Extend rental"><i class="fas fa-plus-circle"></i></button>
                        <?php endif; ?>
                        <?php if ($q['status'] === 'skipped'): ?>
                            <button class="btn btn-sm btn-outline-primary" data-queue-action="recall" data-id="<?= $q['id'] ?>" title="Recall"><i class="fas fa-redo"></i></button>
                        <?php endif; ?>
                        <?php if (!in_array($q['status'], ['completed', 'cancelled'], true)): ?>
                            <a href="<?= url('queues/edit?id=' . $q['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-pen"></i></a>
                        <?php endif; ?>
                        <a href="<?= url('queues/show?id=' . $q['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
