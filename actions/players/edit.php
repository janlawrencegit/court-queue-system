<?php

require_auth();

$id = (int) ($_GET['id'] ?? 0);
$player = Player::find($id);
if (!$player) {
    flash('error', 'Player not found.');
    redirect('players');
}

$title = 'Edit Player';
require BASE_PATH . '/views/players/edit.php';
