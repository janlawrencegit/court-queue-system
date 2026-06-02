<?php

require_auth();

$stats = [
    'total_courts' => 0,
    'active_courts' => 0,
    'players_waiting' => 0,
    'players_serving' => 0,
    'completed_today' => 0,
];
$courts = [];
$recentQueues = [];
$dbError = null;

try {
    $db = Database::get();

    $stats['total_courts'] = (int) $db->query(
        'SELECT COUNT(*) FROM courts WHERE deleted_at IS NULL'
    )->fetchColumn();

    $stats['active_courts'] = (int) $db->query(
        'SELECT COUNT(*) FROM courts WHERE is_active = 1 AND deleted_at IS NULL'
    )->fetchColumn();

    $stats['players_waiting'] = (int) $db->query(
        "SELECT COUNT(*) FROM queues WHERE status = 'waiting' AND deleted_at IS NULL"
    )->fetchColumn();

    $stats['players_serving'] = (int) $db->query(
        "SELECT COUNT(*) FROM queues WHERE status = 'serving' AND deleted_at IS NULL"
    )->fetchColumn();

    $stats['completed_today'] = (int) $db->query(
        "SELECT COUNT(*) FROM queues WHERE status = 'completed' AND completed_at IS NOT NULL AND DATE(completed_at) = CURDATE() AND deleted_at IS NULL"
    )->fetchColumn();

    $stmt = $db->query(
        'SELECT id, court_number, status FROM courts WHERE is_active = 1 AND deleted_at IS NULL ORDER BY display_order, court_number'
    );
    $courts = $stmt->fetchAll();
    foreach ($courts as $i => $court) {
        $cid = (int) $court['id'];
        $courts[$i]['waiting_count'] = Court::waitingCount($cid);
        $cq = Queue::currentForCourt($cid);
        $nq = Queue::nextForCourt($cid);
        $courts[$i]['current_queue'] = $cq;
        $courts[$i]['next_queue'] = $nq;
    }

    $stmt = $db->prepare(
        'SELECT q.id, q.queue_number, q.player_name, q.status, c.court_number
         FROM queues q
         INNER JOIN courts c ON c.id = q.court_id
         WHERE q.deleted_at IS NULL AND DATE(q.created_at) = CURDATE()
         ORDER BY q.created_at DESC
         LIMIT 10'
    );
    $stmt->execute();
    $recentQueues = $stmt->fetchAll();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

$title = 'Dashboard';
require BASE_PATH . '/views/dashboard/index.php';
