<?php

require_admin();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id === auth_id()) {
    json_response(['success' => false, 'message' => 'Cannot change own status'], 422);
}
User::toggleActive($id);
json_response(['success' => true]);
