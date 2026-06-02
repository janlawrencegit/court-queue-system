<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="card col-md-6"><div class="card-body"><form method="POST" action="<?= url('users/update') ?>"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $user['id'] ?>">
<div class="mb-3"><label>Name</label><input name="name" class="form-control" value="<?= e($user['name']) ?>" required></div>
<div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" value="<?= e($user['email']) ?>" required></div>
<div class="mb-3"><label>New Password (optional)</label><input name="password" type="password" class="form-control"></div>
<div class="mb-3"><label>Role</label><select name="role" class="form-select"><?php foreach(['staff','operator','admin'] as $r): ?><option <?= $user['role']===$r?'selected':'' ?>><?= $r ?></option><?php endforeach; ?></select></div>
<div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" <?= $user['is_active']?'checked':'' ?>><label class="form-check-label">Active</label></div>
<button class="btn btn-primary">Update</button></form></div></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
