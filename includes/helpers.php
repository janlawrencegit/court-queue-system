<?php

declare(strict_types=1);

function config(string $key, $default = null)
{
    static $cfg;
    if ($cfg === null) {
        $cfg = require BASE_PATH . '/config/config.php';
    }
    $parts = explode('.', $key);
    $val = $cfg;
    foreach ($parts as $part) {
        if (!is_array($val) || !array_key_exists($part, $val)) {
            return $default;
        }
        $val = $val[$part];
    }
    return $val;
}

function setting(string $key, ?string $default = null): ?string
{
    $all = settings_all();
    return array_key_exists($key, $all) ? $all[$key] : $default;
}

/** @return array<string, string> */
function settings_all(): array
{
    if (isset($GLOBALS['_app_settings_cache']) && is_array($GLOBALS['_app_settings_cache'])) {
        return $GLOBALS['_app_settings_cache'];
    }

    $cache = [];
    try {
        if (class_exists('Database', false)) {
            $stmt = Database::get()->query('SELECT `key`, value FROM settings');
            foreach ($stmt->fetchAll() as $row) {
                $cache[$row['key']] = (string) $row['value'];
            }
        }
    } catch (Throwable $e) {
        // Fall back to config defaults when DB/settings are unavailable.
    }

    $GLOBALS['_app_settings_cache'] = $cache;
    return $cache;
}

function setting_save(string $key, string $value, string $group = 'general', string $type = 'string'): void
{
    Database::get()->prepare(
        'INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at)
         VALUES (?, ?, ?, ?, NOW(), NOW())
         ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()'
    )->execute([$group, $key, $value, $type]);

    settings_clear_cache();
}

function settings_clear_cache(): void
{
    unset($GLOBALS['_app_settings_cache']);
}

function favicon_tags(): void
{
    $href = url('assets/favicon.svg');
    echo '<link rel="icon" href="' . e($href) . '" type="image/svg+xml">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . e($href) . '">' . "\n";
}

function app_name(): string
{
    try {
        $name = setting('system_name');
        if ($name !== null && $name !== '') {
            return $name;
        }
    } catch (Throwable $e) {
        // ignore
    }
    return (string) config('app_name', 'Court Queue System');
}

function app_timezone(): DateTimeZone
{
    return new DateTimeZone((string) config('timezone', 'Asia/Manila'));
}

/** @return array{0: string, 1: string} Start/end datetime for a calendar day in app timezone */
function day_bounds(string $date): array
{
    $start = DateTime::createFromFormat('Y-m-d', $date, app_timezone());
    if (!$start) {
        $start = new DateTime('today', app_timezone());
    } else {
        $start->setTime(0, 0, 0);
    }
    $end = clone $start;
    $end->modify('+1 day');
    return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
}

/** @return array<string, string> */
function skill_levels(): array
{
    return [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'pro' => 'Pro',
    ];
}

function skill_level_label(?string $level): string
{
    $levels = skill_levels();
    $key = strtolower(trim((string) $level));
    return $levels[$key] ?? 'Intermediate';
}

function skill_level_badge(?string $level): string
{
    switch (strtolower(trim((string) $level))) {
        case 'beginner':
            return 'secondary';
        case 'advanced':
            return 'warning';
        case 'pro':
            return 'danger';
        default:
            return 'info';
    }
}

/** 3-letter code for live display (e.g. PRO, INT). */
function skill_level_code(?string $level): string
{
    switch (strtolower(trim((string) $level))) {
        case 'beginner':
            return 'BEG';
        case 'advanced':
            return 'ADV';
        case 'pro':
            return 'PRO';
        default:
            return 'INT';
    }
}

/** CSS modifier for skill badge on live display. */
function skill_level_display_class(?string $level): string
{
    switch (strtolower(trim((string) $level))) {
        case 'beginner':
            return 'beginner';
        case 'advanced':
            return 'advanced';
        case 'pro':
            return 'pro';
        default:
            return 'intermediate';
    }
}

/**
 * @param array<string, mixed>|null $stats row from Player::cardsByIds
 * @return array{name: string, skill_level: string, skill_code: string, skill_class: string, games_played: int}
 */
function player_display_card(int $playerId, string $name, ?array $stats = null): array
{
    $skill = strtolower(trim((string) ($stats['skill_level'] ?? 'intermediate')));
    if (!array_key_exists($skill, skill_levels())) {
        $skill = 'intermediate';
    }

    return [
        'name' => $name,
        'skill_level' => $skill,
        'skill_code' => skill_level_code($skill),
        'skill_class' => skill_level_display_class($skill),
        'games_played' => (int) ($stats['games_played'] ?? 0),
    ];
}

/** @return list<array{name: string, skill_level: string, skill_code: string, skill_class: string, games_played: int}> */
function queue_team_cards(?array $queue, int $teamNum = 1): array
{
    if (!$queue) {
        return [];
    }
    $key = $teamNum === 2 ? 'display_team2_cards' : 'display_team1_cards';
    if (!empty($queue[$key]) && is_array($queue[$key])) {
        return $queue[$key];
    }
    $names = queue_team_members($queue, $teamNum);
    return array_map(static fn($n) => player_display_card(0, (string) $n), $names);
}

/** @return array<string, mixed>|null */
function queue_for_display_api(?array $queue): ?array
{
    if (!$queue) {
        return null;
    }

    return [
        'queue_number' => $queue['queue_number'],
        'served_at' => $queue['served_at'] ?? null,
        'rental_ends_at' => $queue['rental_ends_at'] ?? null,
        'display_team1_cards' => queue_team_cards($queue, 1),
        'display_team2_cards' => queue_team_cards($queue, 2),
    ];
}

function match_type_label(?string $type): string
{
    return strtolower((string) $type) === 'doubles' ? 'Doubles' : 'Singles';
}

/** @param array<string, mixed>|null $queue */
/** One-line match label for reports / CSV */
function queue_match_summary(?array $queue): string
{
    if (!$queue) {
        return '';
    }
    $players = array_values(array_filter(array_merge(
        queue_team_members($queue, 1),
        queue_team_members($queue, 2)
    )));
    return implode(' / ', $players);
}

/** @param array<string, mixed>|null $queue */
function queue_team_members(?array $queue, int $teamNum = 1): array
{
    if (!$queue) {
        return [];
    }
    $key = $teamNum === 2 ? 'display_team2' : 'display_team1';
    if (!empty($queue[$key]) && is_array($queue[$key])) {
        return array_values(array_filter($queue[$key], static fn($n) => trim((string) $n) !== ''));
    }
    $text = trim((string) ($queue[$teamNum === 2 ? 'display_side2' : 'display_side1'] ?? ''));
    if ($text === '') {
        return [];
    }
    if (strpos($text, "\n") !== false) {
        return array_values(array_filter(array_map('trim', explode("\n", $text))));
    }
    if (stripos($text, ' & ') !== false) {
        return array_values(array_filter(array_map('trim', preg_split('/\s+&\s+/u', $text))));
    }
    if (strpos($text, ',') !== false) {
        return array_values(array_filter(array_map('trim', explode(',', $text))));
    }
    return [$text];
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = BASE_URL;
    if (!$base) {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }
    $path = ltrim($path, '/');
    // App lives in htdocs root (e.g. /index.php → base is empty or /)
    if ($base === '' || $base === '/') {
        return $path ? '/' . $path : '/';
    }
    return $path ? $base . '/' . $path : $base;
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function set_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function format_datetime(?string $value, string $empty = '—'): string
{
    if ($value === null || $value === '') {
        return $empty;
    }
    try {
        $dt = new DateTime($value);
        $tz = config('timezone', 'Asia/Manila');
        if ($tz) {
            $dt->setTimezone(new DateTimeZone((string) $tz));
        }
        return $dt->format('M j, Y g:i A');
    } catch (Exception $e) {
        return $empty;
    }
}

/** Elapsed play time in seconds from served_at until completed_at or now. */
function queue_duration_seconds(?array $queue): ?int
{
    if (!$queue || empty($queue['served_at'])) {
        return null;
    }
    try {
        $tz = config('timezone', 'Asia/Manila');
        $tzObj = $tz ? new DateTimeZone((string) $tz) : null;
        $start = new DateTime($queue['served_at'], $tzObj);
        if (!empty($queue['completed_at'])) {
            $end = new DateTime($queue['completed_at'], $tzObj);
        } else {
            $end = new DateTime('now', $tzObj);
        }
        return max(0, $end->getTimestamp() - $start->getTimestamp());
    } catch (Exception $e) {
        return null;
    }
}

function format_play_duration(?int $seconds, string $empty = '—'): string
{
    if ($seconds === null) {
        return $empty;
    }
    if ($seconds < 60) {
        return '<1m';
    }
    $hours = intdiv($seconds, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    if ($hours > 0) {
        return $hours . 'h ' . $minutes . 'm';
    }
    return $minutes . 'm';
}

function queue_play_duration_label(?array $queue): string
{
    return format_play_duration(queue_duration_seconds($queue));
}

/** Minutes exceeded beyond rental_ends_at, if any. */
function queue_overtime_minutes(?array $queue): int
{
    if (!$queue || empty($queue['rental_ends_at'])) {
        return 0;
    }
    try {
        $tz = config('timezone', 'Asia/Manila');
        $tzObj = $tz ? new DateTimeZone((string) $tz) : null;
        $rentalEnd = new DateTime((string) $queue['rental_ends_at'], $tzObj);
        $end = !empty($queue['completed_at'])
            ? new DateTime((string) $queue['completed_at'], $tzObj)
            : new DateTime('now', $tzObj);
        $seconds = $end->getTimestamp() - $rentalEnd->getTimestamp();
        if ($seconds <= 0) {
            return 0;
        }
        // Show full elapsed overtime minutes to avoid overstating.
        return (int) floor($seconds / 60);
    } catch (Exception $e) {
        return 0;
    }
}

function queue_overtime_label(?array $queue): string
{
    $minutes = queue_overtime_minutes($queue);
    if ($minutes <= 0) {
        return '';
    }
    return '+' . $minutes . 'm overtime';
}

function status_badge(string $status): string
{
    switch ($status) {
        case 'available':
        case 'completed':
            return 'success';
        case 'occupied':
        case 'serving':
            return 'warning';
        case 'closed':
        case 'cancelled':
            return 'danger';
        case 'waiting':
            return 'info';
        case 'called':
            return 'primary';
        case 'skipped':
            return 'secondary';
        default:
            return 'secondary';
    }
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function audit_log(string $action, ?string $modelType = null, ?int $modelId = null): void
{
    if (!auth_check()) {
        return;
    }
    $db = Database::get();
    $stmt = $db->prepare(
        'INSERT INTO audit_logs (user_id, action, model_type, model_id, ip_address, user_agent, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())'
    );
    $stmt->execute([
        auth_id(),
        $action,
        $modelType,
        $modelId,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null,
    ]);
}
