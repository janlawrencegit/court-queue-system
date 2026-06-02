<?php

require_auth();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
Player::update($id, [
    'player_name' => trim($_POST['player_name'] ?? ''),
    'skill_level' => $_POST['skill_level'] ?? 'intermediate',
]);

flash('success', 'Player updated.');
redirect('players');
