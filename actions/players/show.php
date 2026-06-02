<?php

require_auth();

$id = (int) ($_GET['id'] ?? 0);
$player = Player::find($id);
if (!$player) {
    flash('error', 'Player not found.');
    redirect('players');
}

$history = Player::queueHistory($id);
$title = $player['player_name'];
require BASE_PATH . '/views/players/show.php';
