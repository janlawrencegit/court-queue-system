<?php
/** @var array<string, mixed> $queue */
/** @var int $teamNum 1 or 2 */
/** @var string $extraClass optional CSS class for each name line */

$teamNum = (int) ($teamNum ?? 1);
$key = $teamNum === 2 ? 'display_team2' : 'display_team1';
$members = [];

if (!empty($queue[$key]) && is_array($queue[$key])) {
    $members = $queue[$key];
} else {
    $text = (string) ($queue[$teamNum === 2 ? 'display_side2' : 'display_side1'] ?? '');
    if (strpos($text, "\n") !== false) {
        $members = array_map('trim', explode("\n", $text));
    } elseif (stripos($text, ' & ') !== false) {
        $members = array_map('trim', preg_split('/\s+&\s+/u', $text));
    } elseif ($text !== '') {
        $members = [$text];
    }
}

$members = array_values(array_filter($members, static fn($n) => $n !== ''));
$lineClass = trim('queue-team-player ' . ($extraClass ?? ''));

if ($members === []) {
    echo '<span class="text-muted">—</span>';
    return;
}

echo '<div class="queue-team-stack">';
foreach ($members as $name) {
    echo '<div class="' . e($lineClass) . '">' . e($name) . '</div>';
}
echo '</div>';
