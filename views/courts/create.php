<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('courts/store') ?>"><?= csrf_field() ?>
<div class="row g-3">
<div class="col-md-6">
    <label class="form-label">Court Number *</label>
    <input name="court_number" class="form-control" required
           value="<?= old('court_number', $suggestedCourtNumber ?? '') ?>"
           placeholder="<?= e($suggestedCourtNumber ?? 'Court 2') ?>">
    <div class="form-text">Displayed as Court 1, Court 2, etc. You can type 1 or Court 1.</div>
</div>
<div class="col-md-6"><label class="form-label">Type</label><select name="court_type" class="form-select"><option <?= old('court_type') === 'standard' ? 'selected' : '' ?>>standard</option><option <?= old('court_type') === 'vip' ? 'selected' : '' ?>>vip</option><option <?= old('court_type') === 'premium' ? 'selected' : '' ?>>premium</option></select></div>
<div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="available" <?= old('status', 'available') === 'available' ? 'selected' : '' ?>>available</option><option value="occupied" <?= old('status') === 'occupied' ? 'selected' : '' ?>>occupied</option><option value="closed" <?= old('status') === 'closed' ? 'selected' : '' ?>>closed</option></select></div>
<div class="col-md-6"><label class="form-label">Capacity</label><input name="capacity" type="number" class="form-control" value="<?= old('capacity', '10') ?>"></div>
<div class="col-md-6"><label class="form-label">Display Order</label><input name="display_order" type="number" class="form-control" value="<?= old('display_order', '0') ?>"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?= old('description') ?></textarea></div>
<div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" <?= ($_SESSION['old']['is_active'] ?? '1') ? 'checked' : '' ?> id="ia"><label for="ia" class="form-check-label">Active</label></div></div>
</div>
<button class="btn btn-primary mt-3">Save Court</button>
<a href="<?= url('courts') ?>" class="btn btn-outline-secondary mt-3">Cancel</a>
</form></div></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
