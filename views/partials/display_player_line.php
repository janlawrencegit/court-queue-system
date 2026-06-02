<?php
/** @var array{name: string, skill_level?: string, skill_code?: string, skill_class?: string, games_played?: int} $player */
$skillCode = $player['skill_code'] ?? skill_level_code($player['skill_level'] ?? 'intermediate');
$skillClass = $player['skill_class'] ?? skill_level_display_class($player['skill_level'] ?? 'intermediate');
$games = (int) ($player['games_played'] ?? 0);
?>
<div class="match-player-line">
    <span class="match-player-name"><?= e($player['name']) ?></span>
    <span class="match-player-meta">
        <span class="player-skill skill--<?= e($skillClass) ?>" title="<?= e(skill_level_label($player['skill_level'] ?? 'intermediate')) ?>"><?= e($skillCode) ?></span>
        <span class="player-times" title="Completed matches"><?= $games ?>×</span>
    </span>
</div>
