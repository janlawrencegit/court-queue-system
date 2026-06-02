<?php

require_auth();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

$ok = false;
switch ($action) {
    case 'call':
        $ok = Queue::call($id);
        break;
    case 'serve':
        $ok = Queue::serve($id);
        break;
    case 'complete':
        $ok = Queue::complete($id);
        break;
    case 'skip':
        $ok = Queue::skip($id);
        break;
    case 'recall':
        $ok = Queue::recall($id);
        break;
    case 'cancel':
        $ok = Queue::cancel($id);
        break;
    case 'extend':
        $ok = Queue::extendRental($id, isset($_POST['minutes']) ? (int) $_POST['minutes'] : null);
        break;
}

if (isset($_POST['ajax'])) {
    json_response(['success' => $ok, 'message' => $ok ? 'OK' : 'Action failed']);
}

if ($ok) {
    flash('success', 'Queue updated.');
} else {
    flash('error', 'Could not update queue.');
}

redirect($_POST['redirect'] ?? 'queues');
