<?php
/** @var array<string, mixed> $court */
/** @var list<array<string, mixed>> $queues */
require BASE_PATH . '/views/layouts/header.php';
?>
<script>document.body.dataset.queueActionUrl = '<?= url('queues/action') ?>';</script>
<p><span class="badge bg-<?= status_badge($court['status']) ?>"><?= e(ucfirst($court['status'])) ?></span></p>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Queue #</th>
                    <th>Match</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($queues)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No queues for this court today.</td></tr>
            <?php endif; ?>
            <?php foreach ($queues as $q): ?>
                <tr>
                    <td><span class="badge bg-dark"><?= e($q['queue_number']) ?></span></td>
                    <td><?php $queue = $q; $inline = true; require BASE_PATH . '/views/partials/queue_match.php'; unset($inline); ?></td>
                    <td class="text-nowrap"><?= format_datetime($q['served_at'] ?? null) ?></td>
                    <td class="text-nowrap"><?= format_datetime($q['completed_at'] ?? null) ?></td>
                    <td><span class="badge bg-<?= status_badge($q['status']) ?>"><?= e(ucfirst($q['status'])) ?></span></td>
                    <td class="text-nowrap">
                        <?php if ($q['status'] === 'waiting'): ?>
                            <button class="btn btn-sm btn-success" data-queue-action="serve" data-id="<?= $q['id'] ?>">Start</button>
                        <?php endif; ?>
                        <?php if (in_array($q['status'], ['waiting', 'called', 'serving'], true)): ?>
                            <button class="btn btn-sm btn-info" data-queue-action="complete" data-id="<?= $q['id'] ?>">End</button>
                        <?php endif; ?>
                        <?php if ($q['status'] === 'serving'): ?>
                            <button class="btn btn-sm btn-danger" data-queue-action="extend" data-id="<?= $q['id'] ?>">Extend</button>
                        <?php endif; ?>
                        <?php if (!in_array($q['status'], ['completed', 'cancelled'], true)): ?>
                            <a href="<?= url('queues/edit?id=' . $q['id']) ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <?php endif; ?>
                        <a href="<?= url('queues/show?id=' . $q['id']) ?>" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
