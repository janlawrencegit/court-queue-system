<?php
/** @var array<string, mixed> $queue */
/** @var list<array<string, mixed>> $courts */
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="card col-lg-8">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-semibold"><i class="fas fa-pen me-2 text-primary"></i>Edit Queue <?= e($queue['queue_number']) ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('queues/update') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $queue['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Court *</label>
                <select name="court_id" class="form-select" required>
                    <?php foreach ($courts as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (int) $queue['court_id'] === (int) $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['court_number']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="0" <?= (int) ($queue['priority'] ?? 0) === 0 ? 'selected' : '' ?>>Normal</option>
                        <option value="5" <?= (int) ($queue['priority'] ?? 0) === 5 ? 'selected' : '' ?>>High</option>
                        <option value="10" <?= (int) ($queue['priority'] ?? 0) === 10 ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Rental minutes</label>
                    <input name="rental_minutes" type="number" min="1" class="form-control"
                           value="<?= e((string) ((int) ($queue['rental_minutes'] ?? (int) (setting('rental_default_minutes', '60') ?? 60)))) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"><?= e((string) ($queue['notes'] ?? '')) ?></textarea>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= url('queues/show?id=' . (int) $queue['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>

