<?php

declare(strict_types=1);

/**
 * Diagnostic page — DELETE after fixing issues
 * Open: https://pickleball.synergize.co/health.php
 */

header('Content-Type: text/html; charset=utf-8');
echo '<h1>Health Check</h1><pre>';

echo 'PHP version: ' . PHP_VERSION . "\n\n";

try {
    require __DIR__ . '/includes/env.php';
    echo "env.php: OK\n";

    require __DIR__ . '/includes/init.php';
    echo "Init: OK\n";
    echo 'APP_URL: ' . env('APP_URL', '(not set)') . "\n";
    echo 'DB_HOST: ' . config('db.host') . "\n";
    echo 'DB_NAME: ' . config('db.name') . "\n";
    echo 'App name: ' . app_name() . "\n\n";

    echo "--- Model classes (must match filename) ---\n";
    foreach (['Database', 'User', 'Court', 'Player', 'Queue'] as $cls) {
        echo $cls . ': ' . (class_exists($cls, false) ? 'OK' : 'MISSING') . "\n";
    }
    echo "\n";

    $db = Database::get();
    echo 'Database: CONNECTED via ' . Database::connectedHost() . "\n\n";

    $tables = ['users', 'courts', 'players', 'queues', 'queue_logs', 'settings'];
    foreach ($tables as $t) {
        try {
            $count = (int) $db->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
            echo "Table {$t}: OK ({$count} rows)\n";
        } catch (Throwable $e) {
            echo "Table {$t}: ERROR - " . $e->getMessage() . "\n";
        }
    }

    echo "\n--- Queue list test ---\n";
    $queues = Queue::all(['date' => date('Y-m-d')]);
    echo 'Queues today: ' . count($queues) . "\n";

    echo "\n--- Dashboard stats test ---\n";
    print_r(Queue::dashboardStats());

    echo "\n--- Courts test ---\n";
    $courts = Court::withQueueInfo();
    echo 'Courts loaded: ' . count($courts) . "\n";

    echo "\n--- Admin user ---\n";
    $admin = User::findByEmail('admin@courtqueue.com');
    if ($admin) {
        echo 'Found: ' . $admin['email'] . "\n";
        echo 'Password verify (password): ' . (password_verify('password', $admin['password']) ? 'YES' : 'NO') . "\n";
    } else {
        echo "Admin user NOT found\n";
    }

    echo "\n\nAll checks passed.\n";
} catch (Throwable $e) {
    echo "\nFATAL ERROR:\n" . $e->getMessage() . "\n\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}

echo '</pre><p><strong>Delete health.php after use.</strong></p>';
