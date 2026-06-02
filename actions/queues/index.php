<?php

require_auth();

$queueError = null;
$queues = [];
try {
    $queues = Queue::all([
        'court_id' => $_GET['court_id'] ?? '',
        'status' => $_GET['status'] ?? '',
        'date' => $_GET['date'] ?? date('Y-m-d'),
        'search' => $_GET['search'] ?? '',
    ]);
} catch (Throwable $e) {
    $queueError = $e->getMessage();
}

$courts = Court::all(['active_only' => true]);
$title = 'Queues';
require BASE_PATH . '/views/queues/index.php';
