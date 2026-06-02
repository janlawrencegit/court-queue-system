<?php

require_auth();
require_roles(['admin', 'operator']);
csrf_verify();

$courtNumber = Court::normalizeCourtNumber((string) ($_POST['court_number'] ?? ''));

if ($courtNumber === '') {
    flash('error', 'Court number is required.');
    set_old($_POST);
    redirect('courts/create');
}

if (Court::courtNumberExists($courtNumber)) {
    flash('error', 'Court number "' . $courtNumber . '" is already in use. Please choose a different number.');
    set_old($_POST);
    redirect('courts/create');
}

try {
    $id = Court::create([
        'court_number' => $courtNumber,
        'court_type' => $_POST['court_type'] ?? 'standard',
        'status' => $_POST['status'] ?? 'available',
        'description' => $_POST['description'] ?? '',
        'capacity' => (int) ($_POST['capacity'] ?? 10),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'display_order' => (int) ($_POST['display_order'] ?? 0),
    ]);
} catch (PDOException $e) {
    if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
        flash('error', 'Court number "' . $courtNumber . '" is already in use.');
        set_old($_POST);
        redirect('courts/create');
    }
    throw $e;
}

audit_log('create', 'Court', $id);
flash('success', 'Court created successfully.');
redirect('courts');
