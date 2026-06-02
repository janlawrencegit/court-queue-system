<?php

require_auth();

$courts = Court::all(['active_only' => true]);
$selected = [
    'player' => null,
    'partner' => null,
    'opponent' => null,
    'opponent_partner' => null,
];
$old = $_SESSION['old'] ?? [];
if (!empty($old['player_id'])) {
    $selected['player'] = Player::find((int) $old['player_id']);
}
if (!empty($old['partner_player_id'])) {
    $selected['partner'] = Player::find((int) $old['partner_player_id']);
}
if (!empty($old['player2_id']) || !empty($old['opponent_player_id'])) {
    $selected['opponent'] = Player::find((int) ($old['player2_id'] ?? $old['opponent_player_id']));
}
if (!empty($old['player4_id']) || !empty($old['opponent_partner_id'])) {
    $selected['opponent_partner'] = Player::find((int) ($old['player4_id'] ?? $old['opponent_partner_id']));
}

$title = 'New Match';
require BASE_PATH . '/views/queues/create.php';
