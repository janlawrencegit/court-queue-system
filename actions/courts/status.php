<?php

require_auth();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
if (!in_array($status, ['available', 'occupied', 'closed'], true)) {
    json_response(['success' => false, 'message' => 'Invalid status'], 422);
}

Court::updateStatus($id, $status);
json_response(['success' => true, 'status' => $status]);
