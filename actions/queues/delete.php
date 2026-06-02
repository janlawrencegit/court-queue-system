<?php

require_auth();
csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
Queue::delete($id);
flash('success', 'Queue deleted.');
redirect('queues');
