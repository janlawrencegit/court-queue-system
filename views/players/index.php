<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between mb-3"><form method="GET" class="d-flex gap-2"><input name="search" class="form-control form-control-sm" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Search"><button class="btn btn-sm btn-primary">Go</button></form>
<a href="<?= url('players/create') ?>" class="btn btn-primary btn-sm">Add Player</a></div>
<div class="card"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Code</th><th>Name</th><th>Skill</th><th>Games</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($players as $p): ?><tr>
<td><span class="badge bg-secondary"><?= e($p['player_code']) ?></span></td>
<td><a href="<?= url('players/show?id=' . $p['id']) ?>" class="fw-semibold text-decoration-none"><?= e($p['player_name']) ?></a></td>
<td><span class="badge bg-<?= skill_level_badge($p['skill_level'] ?? '') ?>"><?= e(skill_level_label($p['skill_level'] ?? '')) ?></span></td>
<td><span class="badge bg-dark"><?= (int) ($p['games_played'] ?? 0) ?></span></td>
<td class="text-nowrap">
<a href="<?= url('players/edit?id=' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
<form method="POST" action="<?= url('players/delete') ?>" class="d-inline" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn btn-sm btn-outline-danger">Del</button></form>
</td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
