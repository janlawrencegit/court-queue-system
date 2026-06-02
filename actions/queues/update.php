<?php

require_auth();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$queue = Queue::find($id);
if (!$queue) {
    flash('error', 'Queue not found.');
    redirect('queues');
}

$courtId = (int) ($_POST['court_id'] ?? 0);
if (!$courtId || !Court::find($courtId)) {
    flash('error', 'Please select a valid court.');
    redirect('queues/edit?id=' . $id);
}

$priority = (int) ($_POST['priority'] ?? 0);
$rentalMinutes = (int) ($_POST['rental_minutes'] ?? 0);
if ($rentalMinutes <= 0) {
    $rentalMinutes = max(1, (int) (setting('rental_default_minutes', '60') ?? 60));
}

$ok = Queue::updateEditable($id, [
    'court_id' => $courtId,
    'priority' => $priority,
    'rental_minutes' => $rentalMinutes,
    'notes' => trim((string) ($_POST['notes'] ?? '')),
]);

if (!$ok) {
    flash('error', 'Queue cannot be edited in its current state.');
    redirect('queues/show?id=' . $id);
}

flash('success', 'Queue updated.');
redirect('queues/show?id=' . $id);

