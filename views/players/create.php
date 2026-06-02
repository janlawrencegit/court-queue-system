<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="card col-md-6"><div class="card-body"><form method="POST" action="<?= url('players/store') ?>"><?= csrf_field() ?>
<div class="mb-3"><label>Name *</label><input name="player_name" class="form-control" value="<?= old('player_name') ?>" required></div>
<div class="mb-3"><label>Skill level *</label>
<select name="skill_level" class="form-select" required>
<?php foreach (skill_levels() as $key => $label): ?>
<option value="<?= e($key) ?>" <?= old('skill_level', 'intermediate') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
</select></div>
<button class="btn btn-primary">Save</button></form></div></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
