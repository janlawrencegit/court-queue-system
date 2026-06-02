<?php

$courts = Court::withQueueInfo();
$data = array_map(function ($c) {
    return [
        'id' => $c['id'],
        'court_number' => $c['court_number'],
        'status' => $c['status'],
        'current_queue' => queue_for_display_api($c['current_queue'] ?? null),
        'next_queue' => queue_for_display_api($c['next_queue'] ?? null),
        'waiting_count' => $c['waiting_count'],
        'waiting_queues' => array_map(static function ($wq) {
            return queue_for_display_api($wq);
        }, $c['waiting_queues'] ?? []),
    ];
}, $courts);

$waitlist = array_map(static function (array $item) {
    $payload = queue_for_display_api($item['queue']);
    if ($payload === null) {
        return null;
    }
    return array_merge([
        'court_id' => $item['court_id'],
        'court_number' => $item['court_number'],
        'position' => $item['position'],
        'is_next' => $item['is_next'],
    ], $payload);
}, Queue::globalWaitlistForDisplay());

$waitlist = array_values(array_filter($waitlist));

json_response([
    'success' => true,
    'courts' => $data,
    'waitlist' => $waitlist,
    'updated_at' => date('H:i:s'),
]);
