<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="card col-md-6"><div class="card-body"><form method="POST" action="<?= url('users/store') ?>"><?= csrf_field() ?>
<div class="mb-3"><label>Name</label><input name="name" class="form-control" required></div>
<div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
<div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required></div>
<div class="mb-3"><label>Role</label><select name="role" class="form-select"><option>staff</option><option>operator</option><option>admin</option></select></div>
<div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Active</label></div>
<button class="btn btn-primary">Create</button></form></div></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
