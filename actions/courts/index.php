<?php

require_auth();
require_roles(['admin', 'operator', 'staff']);

$courts = Court::all([
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'court_type' => $_GET['court_type'] ?? '',
]);

foreach ($courts as &$c) {
    $c['waiting_count'] = Court::waitingCount((int) $c['id']);
    $c['today_completed'] = Court::todayCompleted((int) $c['id']);
}
unset($c);

$title = 'Courts';
require BASE_PATH . '/views/courts/index.php';
