<?php

require_admin();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id === auth_id()) {
    flash('error', 'Cannot delete your own account.');
    redirect('users');
}
User::delete($id);
flash('success', 'User deleted.');
redirect('users');
