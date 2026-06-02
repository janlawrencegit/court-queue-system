<?php

require_auth();
csrf_verify();

Player::create([
    'player_name' => trim($_POST['player_name'] ?? ''),
    'skill_level' => $_POST['skill_level'] ?? 'intermediate',
]);

flash('success', 'Player created.');
redirect('players');
