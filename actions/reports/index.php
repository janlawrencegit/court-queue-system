<?php

require_auth();

$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$courtId = $_GET['court_id'] ?? '';

[$rangeStart] = day_bounds($dateFrom);
[, $rangeEnd] = day_bounds($dateTo);

$db = Database::get();
$sql = 'SELECT q.*, c.court_number FROM queues q JOIN courts c ON c.id = q.court_id
        WHERE q.deleted_at IS NULL AND q.created_at >= ? AND q.created_at < ?';
$params = [$rangeStart, $rangeEnd];

if ($courtId) {
    $sql .= ' AND q.court_id = ?';
    $params[] = $courtId;
}
$sql .= ' ORDER BY q.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$queues = array_map([Queue::class, 'withDisplayName'], $stmt->fetchAll());

$stats = [
    'total' => count($queues),
    'completed' => count(array_filter($queues, fn($q) => $q['status'] === 'completed')),
    'serving' => count(array_filter($queues, fn($q) => in_array($q['status'], ['serving', 'called'], true))),
    'waiting' => count(array_filter($queues, fn($q) => $q['status'] === 'waiting')),
    'cancelled' => count(array_filter($queues, fn($q) => $q['status'] === 'cancelled')),
];

$courts = Court::all();
$title = 'Reports';
require BASE_PATH . '/views/reports/index.php';
