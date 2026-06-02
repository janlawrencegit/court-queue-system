<?php

declare(strict_types=1);

if (class_exists('Queue', false)) {
    return;
}

class Queue
{
    public static function generateNumber(int $courtId): string
    {
        $today = date('Ymd');
        [$dayStart, $dayEnd] = day_bounds(date('Y-m-d'));
        $stmt = Database::get()->prepare(
            'SELECT queue_number FROM queues WHERE court_id = ? AND created_at >= ? AND created_at < ? ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([$courtId, $dayStart, $dayEnd]);
        $last = $stmt->fetchColumn();
        $next = 1;
        if ($last && preg_match('/(\d{4})$/', (string) $last, $m)) {
            $next = (int) $m[1] + 1;
        }
        return sprintf('Q-%s-%04d', $today, $next);
    }

    public static function all(array $filters = []): array
    {
        $sql = 'SELECT q.*, c.court_number FROM queues q
                JOIN courts c ON c.id = q.court_id
                WHERE q.deleted_at IS NULL';
        $params = [];

        if (!empty($filters['court_id'])) {
            $sql .= ' AND q.court_id = ?';
            $params[] = $filters['court_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND q.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date'])) {
            [$dayStart, $dayEnd] = day_bounds((string) $filters['date']);
            $sql .= ' AND q.created_at >= ? AND q.created_at < ?';
            $params[] = $dayStart;
            $params[] = $dayEnd;
        } else {
            [$dayStart, $dayEnd] = day_bounds(date('Y-m-d'));
            $sql .= ' AND q.created_at >= ? AND q.created_at < ?';
            $params[] = $dayStart;
            $params[] = $dayEnd;
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (q.player_name LIKE ? OR q.queue_number LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            $params[] = $s;
            $params[] = $s;
        }

        $sql .= ' ORDER BY q.created_at DESC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return array_map([self::class, 'withDisplayName'], $stmt->fetchAll());
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT q.*, c.court_number FROM queues q
             JOIN courts c ON c.id = q.court_id
             WHERE q.id = ? AND q.deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::withDisplayName($row) : null;
    }

    public static function create(array $data): int
    {
        $db = Database::get();
        $db->beginTransaction();
        try {
            $queueNumber = self::generateNumber((int) $data['court_id']);
            $id = self::insertRow($db, $data, $queueNumber);

            self::log($id, (int) $data['court_id'], 'created', null, 'waiting');
            $court = Court::find((int) $data['court_id']);
            if ($court && $court['status'] === 'available') {
                Court::updateStatus((int) $data['court_id'], 'occupied');
            }

            $db->commit();
            return $id;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** @param array<string, mixed> $data */
    private static function insertRow(PDO $db, array $data, string $queueNumber): int
    {
        $matchType = ($data['match_type'] ?? 'singles') === 'doubles' ? 'doubles' : 'singles';
        $partySize = $matchType === 'doubles' ? 4 : 2;

        try {
            $stmt = $db->prepare(
                'INSERT INTO queues (court_id, player_id, queue_number, player_name, players_json, contact_number,
                 party_size, match_type, status, priority, rental_minutes, notes, created_by, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'waiting\', ?, ?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([
                $data['court_id'],
                $data['player_id'] ?? null,
                $queueNumber,
                $data['player_name'],
                $data['players_json'] ?? null,
                $data['contact_number'] ?? null,
                $partySize,
                $matchType,
                (int) ($data['priority'] ?? 0),
                $data['rental_minutes'] ?? null,
                $data['notes'] ?? null,
                auth_id(),
            ]);
            return (int) $db->lastInsertId();
        } catch (PDOException $e) {
            // Fallback for older schema
        }

        try {
            $stmt = $db->prepare(
                'INSERT INTO queues (court_id, player_id, queue_number, player_name, players_json, contact_number, party_size, status, priority, notes, created_by, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, \'waiting\', ?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([
                $data['court_id'],
                $data['player_id'] ?? null,
                $queueNumber,
                $data['display_name'] ?? $data['player_name'],
                $data['players_json'] ?? null,
                $data['contact_number'] ?? null,
                $partySize,
                (int) ($data['priority'] ?? 0),
                $data['notes'] ?? null,
                auth_id(),
            ]);
            return (int) $db->lastInsertId();
        } catch (PDOException $e) {
            $combined = trim((string) ($data['display_name'] ?? $data['player_name']));
            $stmt = $db->prepare(
                'INSERT INTO queues (court_id, player_id, queue_number, player_name, contact_number, party_size, status, priority, notes, created_by, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, \'waiting\', ?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([
                $data['court_id'],
                $data['player_id'] ?? null,
                $queueNumber,
                $combined,
                $data['contact_number'] ?? null,
                $partySize,
                (int) ($data['priority'] ?? 0),
                $data['notes'] ?? null,
                auth_id(),
            ]);
            return (int) $db->lastInsertId();
        }
    }

    public static function log(int $queueId, int $courtId, string $action, ?string $oldStatus, ?string $newStatus, ?string $notes = null): void
    {
        Database::get()->prepare(
            'INSERT INTO queue_logs (queue_id, court_id, action, old_status, new_status, notes, performed_by, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        )->execute([$queueId, $courtId, $action, $oldStatus, $newStatus, $notes, auth_id()]);
    }

    public static function logs(int $queueId): array
    {
        $stmt = Database::get()->prepare(
            'SELECT ql.*, u.name AS performer_name FROM queue_logs ql
             LEFT JOIN users u ON u.id = ql.performed_by WHERE ql.queue_id = ? ORDER BY ql.created_at ASC'
        );
        $stmt->execute([$queueId]);
        return $stmt->fetchAll();
    }

    public static function transition(int $id, string $newStatus, string $action): bool
    {
        $queue = self::find($id);
        if (!$queue) {
            return false;
        }

        $db = Database::get();
        $db->beginTransaction();
        try {
            $old = $queue['status'];
            $updates = ['status' => $newStatus, 'updated_by' => auth_id(), 'updated_at' => date('Y-m-d H:i:s')];

            if ($newStatus === 'called') {
                $updates['called_at'] = date('Y-m-d H:i:s');
            }
            if ($newStatus === 'serving') {
                $db->prepare(
                    "UPDATE queues SET status='completed', completed_at=NOW() WHERE court_id=? AND status='serving' AND id!=?"
                )->execute([$queue['court_id'], $id]);
                $updates['served_at'] = date('Y-m-d H:i:s');
                Court::updateStatus((int) $queue['court_id'], 'occupied');
            }
            if ($newStatus === 'completed') {
                $updates['completed_at'] = date('Y-m-d H:i:s');
            }

            $sql = 'UPDATE queues SET status=:status, updated_by=:uid';
            $params = ['status' => $newStatus, 'uid' => auth_id(), 'id' => $id];
            if (isset($updates['called_at'])) {
                $sql .= ', called_at=NOW()';
            }
            if (isset($updates['served_at'])) {
                $sql .= ', served_at=NOW()';
            }
            if (isset($updates['completed_at'])) {
                $sql .= ', completed_at=NOW()';
            }
            $sql .= ' WHERE id=:id';
            $db->prepare(str_replace([':status', ':uid', ':id'], ['?', '?', '?'],
                'UPDATE queues SET status=?, updated_by=?' .
                (isset($updates['called_at']) ? ', called_at=NOW()' : '') .
                (isset($updates['served_at']) ? ', served_at=NOW()' : '') .
                (isset($updates['completed_at']) ? ', completed_at=NOW()' : '') .
                ' WHERE id=?'
            ))->execute([$newStatus, auth_id(), $id]);

            if ($newStatus === 'serving') {
                $minutes = isset($queue['rental_minutes']) ? (int) $queue['rental_minutes'] : 0;
                if ($minutes <= 0) {
                    $minutes = self::defaultRentalMinutes();
                }
                $db->prepare(
                    'UPDATE queues SET rental_ends_at = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?'
                )->execute([$minutes, $id]);
            }

            self::log($id, (int) $queue['court_id'], $action, $old, $newStatus);

            if (in_array($newStatus, ['completed', 'cancelled', 'skipped'], true)) {
                $stmt = $db->prepare(
                    "SELECT COUNT(*) FROM queues WHERE court_id=? AND status IN ('waiting','called','serving') AND deleted_at IS NULL"
                );
                $stmt->execute([$queue['court_id']]);
                if ((int) $stmt->fetchColumn() === 0) {
                    Court::updateStatus((int) $queue['court_id'], 'available');
                }
            }

            $db->commit();
            return true;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function call(int $id): bool
    {
        $q = self::find($id);
        return $q && $q['status'] === 'waiting' && self::transition($id, 'called', 'called');
    }

    public static function serve(int $id): bool
    {
        $q = self::find($id);
        return $q && in_array($q['status'], ['waiting', 'called'], true) && self::transition($id, 'serving', 'serving');
    }

    public static function complete(int $id): bool
    {
        $q = self::find($id);
        return $q && in_array($q['status'], ['waiting', 'called', 'serving'], true) && self::transition($id, 'completed', 'completed');
    }

    public static function skip(int $id): bool
    {
        $q = self::find($id);
        return $q && $q['status'] === 'waiting' && self::transition($id, 'skipped', 'skipped');
    }

    public static function recall(int $id): bool
    {
        $q = self::find($id);
        return $q && $q['status'] === 'skipped' && self::transition($id, 'waiting', 'recalled');
    }

    public static function cancel(int $id): bool
    {
        $q = self::find($id);
        return $q && !in_array($q['status'], ['completed', 'cancelled'], true) && self::transition($id, 'cancelled', 'cancelled');
    }

    public static function extendRental(int $id, ?int $minutes = null): bool
    {
        $q = self::find($id);
        if (!$q || $q['status'] !== 'serving') {
            return false;
        }

        $extendBy = max(1, $minutes ?? self::defaultExtendMinutes());
        $db = Database::get();
        $db->beginTransaction();
        try {
            $db->prepare(
                'UPDATE queues
                 SET rental_ends_at = DATE_ADD(
                        CASE
                            WHEN rental_ends_at IS NULL OR rental_ends_at < NOW() THEN NOW()
                            ELSE rental_ends_at
                        END,
                        INTERVAL ? MINUTE
                    ),
                     updated_by = ?,
                     updated_at = NOW()
                 WHERE id = ?'
            )->execute([$extendBy, auth_id(), $id]);

            self::log($id, (int) $q['court_id'], 'extend_rental', $q['status'], $q['status'], '+' . $extendBy . ' minutes');
            $db->commit();
            return true;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function defaultRentalMinutes(): int
    {
        return max(1, (int) (setting('rental_default_minutes', '60') ?? 60));
    }

    private static function defaultExtendMinutes(): int
    {
        return max(1, (int) (setting('rental_extend_minutes', '30') ?? 30));
    }

    public static function delete(int $id): void
    {
        Database::get()->prepare('UPDATE queues SET deleted_at = NOW() WHERE id = ?')->execute([$id]);
    }

    /** @param array<string, mixed> $data */
    public static function updateEditable(int $id, array $data): bool
    {
        $queue = self::find($id);
        if (!$queue) {
            return false;
        }
        if (in_array($queue['status'], ['completed', 'cancelled'], true)) {
            return false;
        }

        $db = Database::get();
        $db->beginTransaction();
        try {
            $db->prepare(
                'UPDATE queues
                 SET court_id = ?,
                     priority = ?,
                     rental_minutes = ?,
                     notes = ?,
                     updated_by = ?,
                     updated_at = NOW()
                 WHERE id = ?'
            )->execute([
                (int) $data['court_id'],
                (int) $data['priority'],
                isset($data['rental_minutes']) ? (int) $data['rental_minutes'] : null,
                $data['notes'] ?? null,
                auth_id(),
                $id,
            ]);

            // If currently serving, realign rental end to the updated rental minutes.
            if ($queue['status'] === 'serving' && !empty($queue['served_at'])) {
                $minutes = isset($data['rental_minutes']) ? (int) $data['rental_minutes'] : 0;
                if ($minutes <= 0) {
                    $minutes = self::defaultRentalMinutes();
                }
                $db->prepare(
                    'UPDATE queues
                     SET rental_ends_at = DATE_ADD(served_at, INTERVAL ? MINUTE),
                         updated_at = NOW()
                     WHERE id = ?'
                )->execute([$minutes, $id]);
            }

            self::log($id, (int) $queue['court_id'], 'edited', $queue['status'], $queue['status']);
            $db->commit();
            return true;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function queueSelectSql(): string
    {
        return "SELECT q.*,
                (SELECT p.player_name FROM players p WHERE p.id = q.player_id AND p.deleted_at IS NULL LIMIT 1) AS linked_player_name";
    }

    public static function currentForCourt(int $courtId): ?array
    {
        try {
            $stmt = Database::get()->prepare(
                self::queueSelectSql() . "
                 FROM queues q
                 WHERE q.court_id = ? AND q.status = 'serving' AND q.deleted_at IS NULL
                 ORDER BY q.served_at DESC LIMIT 1"
            );
            $stmt->execute([$courtId]);
        } catch (Throwable $e) {
            $stmt = Database::get()->prepare(
                "SELECT q.*,
                    (SELECT p.player_name FROM players p WHERE p.id = q.player_id AND p.deleted_at IS NULL LIMIT 1) AS linked_player_name
                 FROM queues q
                 WHERE q.court_id = ? AND q.status = 'serving' AND q.deleted_at IS NULL
                 ORDER BY q.served_at DESC LIMIT 1"
            );
            $stmt->execute([$courtId]);
        }
        $row = $stmt->fetch();
        return $row ? self::withDisplayName($row) : null;
    }

    public static function nextForCourt(int $courtId): ?array
    {
        try {
            $stmt = Database::get()->prepare(
                self::queueSelectSql() . "
                 FROM queues q
                 WHERE q.court_id = ? AND q.status = 'waiting' AND q.deleted_at IS NULL
                 ORDER BY q.priority DESC, q.created_at ASC LIMIT 1"
            );
            $stmt->execute([$courtId]);
        } catch (Throwable $e) {
            $stmt = Database::get()->prepare(
                "SELECT q.*,
                    (SELECT p.player_name FROM players p WHERE p.id = q.player_id AND p.deleted_at IS NULL LIMIT 1) AS linked_player_name
                 FROM queues q
                 WHERE q.court_id = ? AND q.status = 'waiting' AND q.deleted_at IS NULL
                 ORDER BY q.priority DESC, q.created_at ASC LIMIT 1"
            );
            $stmt->execute([$courtId]);
        }
        $row = $stmt->fetch();
        return $row ? self::withDisplayName($row) : null;
    }

    /** Waiting queues after "Next" (offset skips first in line). @return list<array<string, mixed>> */
    public static function waitingListForCourt(int $courtId, int $offset = 1, int $limit = 12): array
    {
        $offset = max(0, $offset);
        $limit = max(1, min(20, $limit));

        try {
            $stmt = Database::get()->prepare(
                self::queueSelectSql() . "
                 FROM queues q
                 WHERE q.court_id = ? AND q.status = 'waiting' AND q.deleted_at IS NULL
                 ORDER BY q.priority DESC, q.created_at ASC
                 LIMIT " . (int) $limit . ' OFFSET ' . (int) $offset
            );
            $stmt->execute([$courtId]);
        } catch (Throwable $e) {
            $stmt = Database::get()->prepare(
                "SELECT q.*,
                    (SELECT p.player_name FROM players p WHERE p.id = q.player_id AND p.deleted_at IS NULL LIMIT 1) AS linked_player_name
                 FROM queues q
                 WHERE q.court_id = ? AND q.status = 'waiting' AND q.deleted_at IS NULL
                 ORDER BY q.priority DESC, q.created_at ASC
                 LIMIT " . (int) $limit . ' OFFSET ' . (int) $offset
            );
            $stmt->execute([$courtId]);
        }

        return array_map([self::class, 'withDisplayName'], $stmt->fetchAll());
    }

    /**
     * All waiting matches for live display sidebar (per-court position #1, #2, …).
     *
     * @return list<array{court_id: int, court_number: string, position: int, is_next: bool, queue: array<string, mixed>}>
     */
    public static function globalWaitlistForDisplay(int $limitPerCourt = 30): array
    {
        $items = [];
        foreach (Court::all(['active_only' => true]) as $court) {
            $courtId = (int) $court['id'];
            $position = 1;
            foreach (self::waitingListForCourt($courtId, 0, $limitPerCourt) as $queue) {
                $items[] = [
                    'court_id' => $courtId,
                    'court_number' => (string) $court['court_number'],
                    'position' => $position,
                    'is_next' => $position === 1,
                    'queue' => $queue,
                ];
                $position++;
            }
        }
        return $items;
    }

    /** @param array<string, mixed> $row */
    public static function withDisplayName(array $row): array
    {
        $allPlayers = self::resolvePlayers($row);
        $team1 = array_slice($allPlayers, 0, min(2, count($allPlayers)));
        $team2 = array_slice($allPlayers, 2, 2);

        if ($team2 === [] && count($team1) === 1 && preg_match('/^(.+?)\s+VS\.?\s+(.+)$/iu', $team1[0], $m)) {
            $team1 = self::splitLegacyTeamLabel(trim($m[1]));
            $team2 = self::splitLegacyTeamLabel(trim($m[2]));
        }

        $row['display_team1'] = $team1;
        $row['display_team2'] = $team2;
        $row['display_team1_cards'] = self::resolveTeamPlayerCards($row, 1);
        $row['display_team2_cards'] = self::resolveTeamPlayerCards($row, 2);
        $row['display_side1'] = implode("\n", $team1);
        $row['display_side2'] = implode("\n", $team2);
        $row['display_name'] = implode(' / ', array_values(array_filter(array_merge($team1, $team2))));

        $row['match_type'] = ($row['match_type'] ?? '') === 'doubles' ? 'doubles' : 'singles';
        if (isset($row['court_number'])) {
            $row['court_number'] = Court::displayCourtNumber((string) $row['court_number']);
        }

        return $row;
    }

    /**
     * @return list<array{name: string, skill_level: string, skill_code: string, skill_class: string, games_played: int}>
     */
    private static function resolveTeamPlayerCards(array $row, int $teamNum): array
    {
        $all = self::resolvePlayerEntries($row);
        $slice = $teamNum === 2 ? array_slice($all, 2, 2) : array_slice($all, 0, 2);
        $ids = array_values(array_filter(array_map(static function ($entry) {
            return (int) ($entry['id'] ?? 0);
        }, $slice)));

        $stats = Player::cardsByIds($ids);
        $cards = [];
        foreach ($slice as $slot) {
            $cards[] = player_display_card(
                (int) ($slot['id'] ?? 0),
                (string) ($slot['name'] ?? ''),
                $stats[(int) ($slot['id'] ?? 0)] ?? null
            );
        }

        return $cards;
    }

    /** @return list<array{id:int,name:string}> */
    private static function resolvePlayerEntries(array $row): array
    {
        $decoded = json_decode((string) ($row['players_json'] ?? ''), true);
        if (is_array($decoded)) {
            $out = [];
            foreach ($decoded as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $name = trim((string) ($item['name'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $out[] = ['id' => (int) ($item['id'] ?? 0), 'name' => $name];
            }
            if ($out !== []) {
                return $out;
            }
        }

        // Legacy fallback for older rows/DBs.
        $legacy = [];
        foreach ([
            ['id' => (int) ($row['player_id'] ?? 0), 'name' => trim((string) ($row['linked_player_name'] ?? ($row['player_name'] ?? '')))],
            ['id' => (int) ($row['partner_player_id'] ?? 0), 'name' => trim((string) ($row['linked_partner_name'] ?? ($row['partner_name'] ?? '')))],
            ['id' => (int) ($row['player2_id'] ?? ($row['opponent_player_id'] ?? 0)), 'name' => trim((string) ($row['linked_player2_name'] ?? ($row['linked_opponent_name'] ?? ($row['player2_name'] ?? ($row['opponent_name'] ?? '')))))],
            ['id' => (int) ($row['player4_id'] ?? ($row['opponent_partner_id'] ?? 0)), 'name' => trim((string) ($row['linked_player4_name'] ?? ($row['linked_opponent_partner_name'] ?? ($row['player4_name'] ?? ($row['opponent_partner_name'] ?? '')))))],
        ] as $entry) {
            if ($entry['name'] !== '') {
                $legacy[] = $entry;
            }
        }
        return $legacy;
    }

    /** @return list<string> */
    private static function splitLegacyTeamLabel(string $label): array
    {
        if (strpos($label, "\n") !== false) {
            return array_values(array_filter(array_map('trim', explode("\n", $label))));
        }
        if (stripos($label, ' & ') !== false) {
            return array_values(array_filter(array_map('trim', preg_split('/\s+&\s+/u', $label))));
        }
        if (strpos($label, ',') !== false) {
            return array_values(array_filter(array_map('trim', explode(',', $label))));
        }
        return $label !== '' ? [$label] : [];
    }

    /** @return list<string> */
    private static function resolvePlayers(array $row): array
    {
        return array_values(array_map(static function ($entry) {
            return (string) $entry['name'];
        }, self::resolvePlayerEntries($row)));
    }

    public static function dashboardStats(): array
    {
        $db = Database::get();
        return [
            'total_courts' => (int) $db->query('SELECT COUNT(*) FROM courts WHERE deleted_at IS NULL')->fetchColumn(),
            'active_courts' => (int) $db->query('SELECT COUNT(*) FROM courts WHERE is_active=1 AND deleted_at IS NULL')->fetchColumn(),
            'players_waiting' => (int) $db->query("SELECT COUNT(*) FROM queues WHERE status='waiting' AND deleted_at IS NULL")->fetchColumn(),
            'players_serving' => (int) $db->query("SELECT COUNT(*) FROM queues WHERE status='serving' AND deleted_at IS NULL")->fetchColumn(),
            'completed_today' => (int) $db->query(
                "SELECT COUNT(*) FROM queues WHERE status='completed' AND completed_at IS NOT NULL AND DATE(completed_at)=CURDATE() AND deleted_at IS NULL"
            )->fetchColumn(),
        ];
    }

    public static function forCourtToday(int $courtId): array
    {
        [$dayStart, $dayEnd] = day_bounds(date('Y-m-d'));
        $stmt = Database::get()->prepare(
            'SELECT * FROM queues WHERE court_id = ? AND created_at >= ? AND created_at < ? AND deleted_at IS NULL ORDER BY created_at DESC'
        );
        $stmt->execute([$courtId, $dayStart, $dayEnd]);
        return array_map([self::class, 'withDisplayName'], $stmt->fetchAll());
    }
}
