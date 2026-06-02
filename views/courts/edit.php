<?php
/** @var array<string, mixed> $court */
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('courts/update') ?>"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $court['id'] ?>">
<div class="row g-3">
<div class="col-md-6">
    <label class="form-label">Court Number *</label>
    <input name="court_number" class="form-control" value="<?= old('court_number', $court['court_number']) ?>" required>
    <div class="form-text">Displayed as Court 1, Court 2, etc. You can type 1 or Court 1.</div>
</div>
<div class="col-md-6"><label class="form-label">Type</label><select name="court_type" class="form-select"><?php foreach (['standard','vip','premium'] as $t): ?><option <?= $court['court_type']===$t?'selected':'' ?>><?= $t ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach (['available','occupied','closed'] as $s): ?><option value="<?= $s ?>" <?= $court['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Capacity</label><input name="capacity" type="number" class="form-control" value="<?= (int)$court['capacity'] ?>"></div>
<div class="col-md-6"><label class="form-label">Display Order</label><input name="display_order" type="number" class="form-control" value="<?= (int)$court['display_order'] ?>"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?= e($court['description'] ?? '') ?></textarea></div>
<div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" <?= $court['is_active']?'checked':'' ?> id="ia"><label for="ia" class="form-check-label">Active</label></div></div>
</div>
<button class="btn btn-primary mt-3">Update</button>
</form></div></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
