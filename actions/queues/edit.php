<?php

require_auth();

$id = (int) ($_GET['id'] ?? 0);
$queue = Queue::find($id);
if (!$queue) {
    flash('error', 'Queue not found.');
    redirect('queues');
}

if (in_array($queue['status'], ['completed', 'cancelled'], true)) {
    flash('error', 'Completed or cancelled queues can no longer be edited.');
    redirect('queues/show?id=' . $id);
}

$courts = Court::all(['active_only' => true]);
$title = 'Edit Queue ' . $queue['queue_number'];
require BASE_PATH . '/views/queues/edit.php';

