<?php

require_auth();
require_roles(['admin', 'operator']);
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if (!Court::find($id)) {
    flash('error', 'Court not found.');
    redirect('courts');
}

$courtNumber = Court::normalizeCourtNumber((string) ($_POST['court_number'] ?? ''));

if ($courtNumber === '') {
    flash('error', 'Court number is required.');
    set_old($_POST);
    redirect('courts/edit?id=' . $id);
}

if (Court::courtNumberExists($courtNumber, $id)) {
    flash('error', 'Court number "' . $courtNumber . '" is already used by another court.');
    set_old($_POST);
    redirect('courts/edit?id=' . $id);
}

$data = [
    'court_number' => $courtNumber,
    'court_type' => $_POST['court_type'] ?? 'standard',
    'status' => $_POST['status'] ?? 'available',
    'description' => $_POST['description'] ?? '',
    'capacity' => (int) ($_POST['capacity'] ?? 10),
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
    'display_order' => (int) ($_POST['display_order'] ?? 0),
];

try {
    Court::update($id, $data);
} catch (PDOException $e) {
    if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
        flash('error', 'Court number "' . $courtNumber . '" is already in use.');
        set_old($_POST);
        redirect('courts/edit?id=' . $id);
    }
    throw $e;
}

audit_log('update', 'Court', $id);
flash('success', 'Court updated.');
redirect('courts');
