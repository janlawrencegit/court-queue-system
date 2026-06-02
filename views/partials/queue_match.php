<?php
/** @var array<string, mixed> $queue */
$inline = !empty($inline);
$team1 = $queue['display_team1'] ?? queue_team_members($queue, 1);
$team2 = $queue['display_team2'] ?? queue_team_members($queue, 2);
if (!is_array($team1)) {
    $team1 = $team1 !== '' ? [$team1] : [];
}
if (!is_array($team2)) {
    $team2 = $team2 !== '' ? [$team2] : [];
}
?>
<?php $players = array_values(array_filter(array_merge($team1, $team2), static function ($n) {
    return trim((string) $n) !== '';
})); ?>
<?php if ($inline): ?>
<span class="queue-match-inline queue-match-singles-col">
    <?php foreach ($players as $n): ?><span class="qm-line"><?= e($n) ?></span><?php endforeach; ?>
</span>
<?php else: ?>
<div class="fw-semibold">
    <?php foreach ($players as $n): ?><div><?= e($n) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>
