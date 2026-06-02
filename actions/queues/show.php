<?php

require_auth();

$id = (int) ($_GET['id'] ?? 0);
$queue = Queue::find($id);
if (!$queue) {
    flash('error', 'Queue not found.');
    redirect('queues');
}

$logs = Queue::logs($id);
$title = 'Queue ' . $queue['queue_number'];
require BASE_PATH . '/views/queues/show.php';
