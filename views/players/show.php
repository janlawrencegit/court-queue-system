<?php
/** @var array<string, mixed> $player */
/** @var list<array<string, mixed>> $history */
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="card mb-3"><div class="card-body">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="mb-1"><?= e($player['player_name']) ?></h4>
            <span class="badge bg-<?= skill_level_badge($player['skill_level'] ?? '') ?>"><?= e(skill_level_label($player['skill_level'] ?? '')) ?></span>
            <span class="badge bg-dark ms-1"><?= (int) ($player['games_played'] ?? 0) ?> games played</span>
        </div>
        <a href="<?= url('players/edit?id=' . $player['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
    </div>
    <p class="text-muted mb-0 mt-2 small">Code: <?= e($player['player_code']) ?></p>
</div></div>
<div class="card"><div class="card-header">Match history</div><table class="table mb-0"><thead><tr><th>Queue</th><th>Match</th><th>Court</th><th>Status</th></tr></thead><tbody>
<?php if (empty($history)): ?><tr><td colspan="4" class="text-center text-muted">No matches yet.</td></tr><?php endif; ?>
<?php foreach ($history as $h): ?><tr>
<td><?= e($h['queue_number']) ?></td>
<td><?php $queue = $h; $inline = true; require BASE_PATH . '/views/partials/queue_match.php'; unset($inline); ?></td>
<td><?= e($h['court_number']) ?></td>
<td><span class="badge bg-<?= status_badge($h['status']) ?>"><?= e(ucfirst($h['status'])) ?></span></td>
</tr><?php endforeach; ?>
</tbody></table></div>
<p class="mt-3"><a href="<?= url('players') ?>" class="btn btn-outline-secondary btn-sm">Back to Players</a></p>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
