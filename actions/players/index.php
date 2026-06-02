<?php

require_auth();

$players = Player::all(['search' => $_GET['search'] ?? '']);
$title = 'Players';
require BASE_PATH . '/views/players/index.php';
