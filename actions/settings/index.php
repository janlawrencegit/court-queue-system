<?php

require_admin();

$settings = settings_all();
$title = 'Settings';
require BASE_PATH . '/views/settings/index.php';
