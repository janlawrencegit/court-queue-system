<?php

$courts = Court::withQueueInfo();
$globalWaitlist = Queue::globalWaitlistForDisplay();
require BASE_PATH . '/views/display/index.php';
