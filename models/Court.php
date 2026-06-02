<?php

declare(strict_types=1);

class Court
{
    public static function displayCourtNumber(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^\d+$/', $value)) {
            return 'Court ' . $value;
        }
        if (preg_match('/^court\s*(\d+)$/iu', $value, $m)) {
            return 'Court ' . $m[1];
        }
        if (stripos($value, 'court ') !== 0) {
            return 'Court ' . $value;
        }
        return 'Court ' . trim(substr($value, 5));
    }

    public static function normalizeCourtNumber(string $value): string
    {
        return self::displayCourtNumber($value);
    }

    public static function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM courts WHERE deleted_at IS NULL';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND court_number LIKE ?';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['court_type'])) {
            $sql .= ' AND court_type = ?';
            $params[] = $filters['court_type'];
        }
        if (!empty($filters['active_only'])) {
            $sql .= ' AND is_active = 1';
        }

        $sql .= ' ORDER BY display_order ASC, court_number ASC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['court_number'] = self::displayCourtNumber((string) ($row['court_number'] ?? ''));
        }
        return $rows;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::get()->prepare('SELECT * FROM courts WHERE id = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $row['court_number'] = self::displayCourtNumber((string) ($row['court_number'] ?? ''));
        }
        return $row ?: null;
    }

    public static function courtNumberExists(string $courtNumber, ?int $excludeId = null): bool
    {
        $courtNumber = self::normalizeCourtNumber($courtNumber);
        if ($courtNumber === '') {
            return false;
        }
        $sql = 'SELECT id FROM courts WHERE court_number = ? AND deleted_at IS NULL';
        $params = [$courtNumber];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' LIMIT 1';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public static function suggestNextCourtNumber(): string
    {
        $max = 0;
        foreach (self::all() as $court) {
            $label = self::normalizeCourtNumber((string) $court['court_number']);
            if (preg_match('/(\d+)\s*$/', $label, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }
        return (string) ($max + 1);
    }

    public static function create(array $data): int
    {
        $courtNumber = self::normalizeCourtNumber((string) ($data['court_number'] ?? ''));
        $stmt = Database::get()->prepare(
            'INSERT INTO courts (court_number, court_type, status, description, capacity, is_active, display_order, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $courtNumber,
            $data['court_type'],
            $data['status'],
            $data['description'] ?? null,
            (int) ($data['capacity'] ?? 10),
            (int) ($data['is_active'] ?? 1),
            (int) ($data['display_order'] ?? 0),
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $courtNumber = self::normalizeCourtNumber((string) ($data['court_number'] ?? ''));
        $stmt = Database::get()->prepare(
            'UPDATE courts SET court_number=?, court_type=?, status=?, description=?, capacity=?, is_active=?, display_order=?, updated_at=NOW() WHERE id=?'
        );
        $stmt->execute([
            $courtNumber,
            $data['court_type'],
            $data['status'],
            $data['description'] ?? null,
            (int) $data['capacity'],
            (int) ($data['is_active'] ?? 1),
            (int) ($data['display_order'] ?? 0),
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        Database::get()->prepare('UPDATE courts SET deleted_at = NOW() WHERE id = ?')->execute([$id]);
    }

    public static function updateStatus(int $id, string $status): void
    {
        Database::get()->prepare('UPDATE courts SET status = ?, updated_at = NOW() WHERE id = ?')->execute([$status, $id]);
    }

    public static function waitingCount(int $courtId): int
    {
        $stmt = Database::get()->prepare("SELECT COUNT(*) FROM queues WHERE court_id = ? AND status = 'waiting' AND deleted_at IS NULL");
        $stmt->execute([$courtId]);
        return (int) $stmt->fetchColumn();
    }

    public static function todayCompleted(int $courtId): int
    {
        $stmt = Database::get()->prepare(
            "SELECT COUNT(*) FROM queues WHERE court_id = ? AND status = 'completed' AND DATE(completed_at) = CURDATE() AND deleted_at IS NULL"
        );
        $stmt->execute([$courtId]);
        return (int) $stmt->fetchColumn();
    }

    public static function withQueueInfo(): array
    {
        $courts = self::all(['active_only' => true]);
        $result = [];
        foreach ($courts as $court) {
            $id = (int) $court['id'];
            $court['waiting_count'] = self::waitingCount($id);
            $court['current_queue'] = Queue::currentForCourt($id);
            $court['next_queue'] = Queue::nextForCourt($id);
            $court['waiting_queues'] = Queue::waitingListForCourt($id, 1, 12);
            $result[] = $court;
        }
        return $result;
    }
}
