<?php

require_admin();
csrf_verify();

$groups = [
    'system_name' => 'general',
    'organization_name' => 'general',
    'contact_email' => 'general',
    'contact_phone' => 'general',
    'display_refresh_interval' => 'display',
    'show_waiting_count' => 'display',
    'display_theme' => 'display',
    'rental_default_minutes' => 'display',
    'rental_extend_minutes' => 'display',
    'queue_prefix' => 'queue',
    'reset_queue_daily' => 'queue',
    'default_party_size' => 'queue',
    'enable_sound' => 'notifications',
    'call_message' => 'notifications',
];

foreach ($_POST as $key => $value) {
    if ($key === 'csrf_token') {
        continue;
    }
    $group = $groups[$key] ?? 'general';
    setting_save($key, (string) $value, $group);
}

flash('success', 'Settings saved.');
redirect('settings');
