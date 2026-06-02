<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<a href="<?= url('users/create') ?>" class="btn btn-primary btn-sm mb-3">Add User</a>
<div class="card"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th></th></tr></thead><tbody>
<?php foreach($users as $u): ?><tr>
<td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td>
<td><?= $u['is_active']?'<span class="badge bg-success">Yes</span>':'<span class="badge bg-secondary">No</span>' ?></td>
<td><a href="<?= url('users/edit?id='.$u['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a></td>
</tr><?php endforeach; ?>
</tbody></table></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
