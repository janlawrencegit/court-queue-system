<?php

require_auth();
require_roles(['admin', 'operator']);

$title = 'Add Court';
$suggestedCourtNumber = Court::suggestNextCourtNumber();
require BASE_PATH . '/views/courts/create.php';
