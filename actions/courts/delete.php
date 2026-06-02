<?php

require_auth();
require_roles(['admin', 'operator']);
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
Court::delete($id);
audit_log('delete', 'Court', $id);
flash('success', 'Court deleted.');
redirect('courts');
