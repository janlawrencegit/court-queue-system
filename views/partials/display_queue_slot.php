<?php
/** @var array<string, mixed>|null $queue */
/** @var string $variant 'now'|'next' */
$team1Cards = queue_team_cards($queue, 1);
if (!$queue || $team1Cards === []) {
    echo '<div class="empty-slot" aria-hidden="true">—</div>';
    return;
}
$team2Cards = queue_team_cards($queue, 2);
$hideTags = !empty($hideTags);
?>
<?php if (!$hideTags): ?>
<div class="slot-tags">
    <span class="tag tag-qnum"><?= e($queue['queue_number']) ?></span>
</div>
<?php endif; ?>
<div class="match-row match-row--teams">
    <div class="match-team">
        <?php foreach (array_merge($team1Cards, $team2Cards) as $player): ?>
            <?php require BASE_PATH . '/views/partials/display_player_line.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
