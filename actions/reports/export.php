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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="queue-report-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Queue #', 'Court', 'Match', 'Status', 'Start', 'End', 'Duration', 'Created']);

while ($row = $stmt->fetch()) {
    $row = Queue::withDisplayName($row);
    fputcsv($out, [
        $row['queue_number'],
        $row['court_number'],
        queue_match_summary($row),
        $row['status'],
        $row['served_at'] ?? '',
        $row['completed_at'] ?? '',
        queue_play_duration_label($row),
        $row['created_at'] ?? '',
    ]);
}

fclose($out);
exit;
