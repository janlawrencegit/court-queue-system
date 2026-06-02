<?php

declare(strict_types=1);

class Player
{
    public static function gamesPlayed(int $playerId): int
    {
        return self::gamesPlayedBatch([$playerId])[$playerId] ?? 0;
    }

    /** @param list<int> $playerIds @return array<int,int> */
    private static function gamesPlayedBatch(array $playerIds): array
    {
        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds))));
        if ($playerIds === []) {
            return [];
        }
        $counts = array_fill_keys($playerIds, 0);

        try {
            $stmt = Database::get()->query(
                "SELECT player_id, players_json
                 FROM queues
                 WHERE deleted_at IS NULL AND status = 'completed'"
            );
            foreach ($stmt->fetchAll() as $row) {
                $present = [];
                $pid = (int) ($row['player_id'] ?? 0);
                if ($pid > 0) {
                    $present[$pid] = true;
                }
                $decoded = json_decode((string) ($row['players_json'] ?? ''), true);
                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        if (!is_array($item)) {
                            continue;
                        }
                        $id = (int) ($item['id'] ?? 0);
                        if ($id > 0) {
                            $present[$id] = true;
                        }
                    }
                }
                foreach ($playerIds as $targetId) {
                    if (isset($present[$targetId])) {
                        $counts[$targetId]++;
                    }
                }
            }
            return $counts;
        } catch (Throwable $e) {
            try {
                $stmt = Database::get()->prepare(
                    "SELECT COUNT(*) FROM queues q
                     WHERE q.deleted_at IS NULL AND q.status = 'completed'
                     AND (q.player_id = ? OR q.opponent_player_id = ?)"
                );
                foreach ($playerIds as $targetId) {
                    $stmt->execute([$targetId, $targetId]);
                    $counts[$targetId] = (int) $stmt->fetchColumn();
                }
                return $counts;
            } catch (Throwable $e2) {
                return $counts;
            }
        }
    }

    public static function all(array $filters = []): array
    {
        $sql = 'SELECT p.* FROM players p WHERE p.deleted_at IS NULL';

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (p.player_name LIKE ? OR p.player_code LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            $params = [$s, $s];
        }

        $sql .= ' ORDER BY p.player_name ASC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $counts = self::gamesPlayedBatch(array_map(static function ($r) {
            return (int) $r['id'];
        }, $rows));
        foreach ($rows as &$row) {
            $row['games_played'] = $counts[(int) $row['id']] ?? 0;
        }
        return $rows;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::get()->prepare('SELECT * FROM players WHERE id = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $row['games_played'] = self::gamesPlayed($id);
        return $row;
    }

    /**
     * Batch load skill + completed match count for live display.
     *
     * @param list<int> $ids
     * @return array<int, array{skill_level: string, games_played: int}>
     */
    public static function cardsByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }

        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = Database::get()->prepare(
            "SELECT p.id, p.skill_level
             FROM players p
             WHERE p.id IN ({$in}) AND p.deleted_at IS NULL"
        );
        $stmt->execute($ids);
        $counts = self::gamesPlayedBatch($ids);

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int) $row['id']] = [
                'skill_level' => (string) ($row['skill_level'] ?? 'intermediate'),
                'games_played' => (int) ($counts[(int) $row['id']] ?? 0),
            ];
        }
        return $map;
    }

    public static function search(string $q, array $excludeIds = []): array
    {
        $excludeIds = array_values(array_unique(array_filter(array_map('intval', $excludeIds))));
        $excludeSql = '';
        if ($excludeIds !== []) {
            $excludeSql = ' AND p.id NOT IN (' . implode(',', array_fill(0, count($excludeIds), '?')) . ')';
        }

        $select = 'SELECT id, player_name, player_code, skill_level';
        if (trim($q) === '') {
            $stmt = Database::get()->prepare(
                $select . ' FROM players p WHERE p.deleted_at IS NULL' . $excludeSql . ' ORDER BY p.player_name ASC LIMIT 20'
            );
            $stmt->execute($excludeIds);
            return self::formatSearchRows($stmt->fetchAll());
        }

        $s = '%' . $q . '%';
        $params = array_merge([$s, $s], $excludeIds);
        $stmt = Database::get()->prepare(
            $select . ' FROM players p WHERE p.deleted_at IS NULL
             AND (player_name LIKE ? OR player_code LIKE ?)' . $excludeSql . '
             ORDER BY player_name ASC LIMIT 10'
        );
        $stmt->execute($params);
        return self::formatSearchRows($stmt->fetchAll());
    }

    /** @param array<int, array<string, mixed>> $rows */
    private static function formatSearchRows(array $rows): array
    {
        foreach ($rows as &$row) {
            if (!isset($row['games_played'])) {
                $row['games_played'] = self::gamesPlayed((int) $row['id']);
            }
            $row['skill_label'] = skill_level_label($row['skill_level'] ?? 'intermediate');
        }
        return $rows;
    }

    public static function create(array $data): int
    {
        $code = $data['player_code'] ?? ('P-' . strtoupper(substr(uniqid(), -8)));
        $skill = self::normalizeSkill($data['skill_level'] ?? 'intermediate');
        try {
            $stmt = Database::get()->prepare(
                'INSERT INTO players (player_name, player_code, skill_level, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([$data['player_name'], $code, $skill]);
        } catch (Throwable $e) {
            $stmt = Database::get()->prepare(
                'INSERT INTO players (player_name, player_code, created_at, updated_at)
                 VALUES (?, ?, NOW(), NOW())'
            );
            $stmt->execute([$data['player_name'], $code]);
        }
        return (int) Database::get()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $skill = self::normalizeSkill($data['skill_level'] ?? 'intermediate');
        try {
            $stmt = Database::get()->prepare(
                'UPDATE players SET player_name=?, skill_level=?, updated_at=NOW() WHERE id=?'
            );
            $stmt->execute([$data['player_name'], $skill, $id]);
        } catch (Throwable $e) {
            Database::get()->prepare(
                'UPDATE players SET player_name=?, updated_at=NOW() WHERE id=?'
            )->execute([$data['player_name'], $id]);
        }
    }

    public static function delete(int $id): void
    {
        Database::get()->prepare('UPDATE players SET deleted_at = NOW() WHERE id = ?')->execute([$id]);
    }

    public static function queueHistory(int $id): array
    {
        try {
            $stmt = Database::get()->prepare(
                'SELECT q.*, c.court_number FROM queues q
                 JOIN courts c ON c.id = q.court_id
                 WHERE q.deleted_at IS NULL
                 AND (q.player_id = ? OR q.player2_id = ?
                      OR q.partner_player_id = ? OR q.player4_id = ?)
                 ORDER BY q.created_at DESC LIMIT 20'
            );
            $stmt->execute([$id, $id, $id, $id]);
            return array_map([Queue::class, 'withDisplayName'], $stmt->fetchAll());
        } catch (Throwable $e) {
            $stmt = Database::get()->prepare(
                'SELECT q.*, c.court_number FROM queues q
                 JOIN courts c ON c.id = q.court_id
                 WHERE q.player_id = ? AND q.deleted_at IS NULL
                 ORDER BY q.created_at DESC LIMIT 20'
            );
            $stmt->execute([$id]);
            return array_map([Queue::class, 'withDisplayName'], $stmt->fetchAll());
        }
    }

    private static function normalizeSkill(string $level): string
    {
        $key = strtolower(trim($level));
        return array_key_exists($key, skill_levels()) ? $key : 'intermediate';
    }
}
