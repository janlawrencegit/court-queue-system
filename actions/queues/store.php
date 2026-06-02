<?php

require_auth();
csrf_verify();

$ids = [
    (int) ($_POST['player_id'] ?? 0),
    (int) ($_POST['player2_id'] ?? ($_POST['opponent_player_id'] ?? 0)),
    (int) ($_POST['partner_player_id'] ?? 0),
    (int) ($_POST['player4_id'] ?? ($_POST['opponent_partner_id'] ?? 0)),
];

$ids = array_values(array_filter($ids));
if (count($ids) !== count(array_unique($ids))) {
    flash('error', 'All players in a match must be different.');
    set_old($_POST);
    redirect('queues/create');
}
if (count($ids) < 2) {
    flash('error', 'Please select at least 2 players.');
    set_old($_POST);
    redirect('queues/create');
}
$matchType = count($ids) >= 3 ? 'doubles' : 'singles';

$player = Player::find((int) ($_POST['player_id'] ?? 0));
$opponent = Player::find((int) ($_POST['player2_id'] ?? ($_POST['opponent_player_id'] ?? 0)));

if (!$player || !$opponent) {
    flash('error', 'Please select all required players.');
    set_old($_POST);
    redirect('queues/create');
}

$partner = null;
$opponentPartner = null;
if (!empty($_POST['partner_player_id'])) {
    $partner = Player::find((int) ($_POST['partner_player_id'] ?? 0));
    if (!$partner) {
        flash('error', 'Selected Player 3 is invalid.');
        set_old($_POST);
        redirect('queues/create');
    }
}
if (!empty($_POST['player4_id']) || !empty($_POST['opponent_partner_id'])) {
    $opponentPartner = Player::find((int) ($_POST['player4_id'] ?? ($_POST['opponent_partner_id'] ?? 0)));
    if (!$opponentPartner) {
        flash('error', 'Selected Player 4 is invalid.');
        set_old($_POST);
        redirect('queues/create');
    }
}

$courtId = (int) ($_POST['court_id'] ?? 0);
if (!$courtId || !Court::find($courtId)) {
    flash('error', 'Please select a court.');
    set_old($_POST);
    redirect('queues/create');
}

$rentalMinutesRaw = $_POST['rental_minutes'] ?? setting('rental_default_minutes', '60');
$rentalMinutes = (int) $rentalMinutesRaw;
if ($rentalMinutes <= 0) {
    $rentalMinutes = 0;
}

$displayList = implode(' / ', array_values(array_filter([
    $player['player_name'],
    $opponent['player_name'],
    $partner['player_name'] ?? null,
    $opponentPartner['player_name'] ?? null,
])));
$playersList = array_values(array_filter([
    ['id' => (int) $player['id'], 'name' => $player['player_name']],
    ['id' => (int) $opponent['id'], 'name' => $opponent['player_name']],
    $partner ? ['id' => (int) $partner['id'], 'name' => $partner['player_name']] : null,
    $opponentPartner ? ['id' => (int) $opponentPartner['id'], 'name' => $opponentPartner['player_name']] : null,
]));

$id = Queue::create([
    'court_id' => $courtId,
    'match_type' => $matchType,
    'rental_minutes' => $rentalMinutes > 0 ? $rentalMinutes : null,
    'player_id' => (int) $player['id'],
    'player_name' => $player['player_name'],
    'players_json' => json_encode($playersList),
    'display_name' => $displayList,
    'priority' => (int) ($_POST['priority'] ?? 0),
    'notes' => $_POST['notes'] ?? '',
]);

$q = Queue::find($id);
clear_old();
flash('success', 'Match queued: ' . ($q['display_name'] ?? ''));
redirect('queues?date=' . date('Y-m-d'));
