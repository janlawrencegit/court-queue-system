<?php

require_auth();

$id = (int) ($_GET['id'] ?? 0);
$court = Court::find($id);
if (!$court) {
    flash('error', 'Court not found.');
    redirect('courts');
}

$queues = Queue::forCourtToday($id);
$stats = [
    'total_today' => count($queues),
    'waiting' => count(array_filter($queues, fn($q) => $q['status'] === 'waiting')),
    'serving' => count(array_filter($queues, fn($q) => $q['status'] === 'serving')),
    'completed' => count(array_filter($queues, fn($q) => $q['status'] === 'completed')),
];

$title = $court['court_number'];
require BASE_PATH . '/views/courts/show.php';
