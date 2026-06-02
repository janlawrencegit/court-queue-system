<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="card col-lg-8"><div class="card-body"><form method="POST" action="<?= url('settings/save') ?>"><?= csrf_field() ?>
<div class="mb-3"><label>System Name</label><input name="system_name" class="form-control" value="<?= e($settings['system_name'] ?? app_name()) ?>"></div>
<div class="mb-3"><label>Organization</label><input name="organization_name" class="form-control" value="<?= e($settings['organization_name'] ?? '') ?>"></div>
<div class="mb-3"><label>Display Refresh (seconds)</label><input name="display_refresh_interval" type="number" class="form-control" value="<?= e($settings['display_refresh_interval'] ?? '10') ?>"></div>
<hr>
<h6 class="mb-3">Rental Timer</h6>
<div class="mb-3"><label>Default rental time (minutes)</label><input name="rental_default_minutes" type="number" min="1" class="form-control" value="<?= e($settings['rental_default_minutes'] ?? '60') ?>"></div>
<div class="mb-3"><label>Extend step (minutes)</label><input name="rental_extend_minutes" type="number" min="1" class="form-control" value="<?= e($settings['rental_extend_minutes'] ?? '30') ?>"></div>
<p class="text-muted small">System Name appears in the sidebar, login page, and public home page. Rental timer uses these values for start and extend actions.</p>
<button class="btn btn-primary">Save Settings</button></form></div></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
