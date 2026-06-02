<?php

require_auth();
require_roles(['admin', 'operator']);

$id = (int) ($_GET['id'] ?? 0);
$court = Court::find($id);
if (!$court) {
    flash('error', 'Court not found.');
    redirect('courts');
}

$title = 'Edit Court';
require BASE_PATH . '/views/courts/edit.php';
