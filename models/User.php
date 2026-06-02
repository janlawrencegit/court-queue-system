<?php

declare(strict_types=1);

class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT * FROM users WHERE LOWER(email) = LOWER(?) AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::get()->prepare('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM users WHERE deleted_at IS NULL';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (name LIKE ? OR email LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            $params[] = $s;
            $params[] = $s;
        }
        if (!empty($filters['role'])) {
            $sql .= ' AND role = ?';
            $params[] = $filters['role'];
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO users (name, email, password, role, phone, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['phone'] ?? null,
            $data['is_active'] ?? 1,
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $fields = ['name = ?', 'email = ?', 'role = ?', 'phone = ?', 'is_active = ?', 'updated_at = NOW()'];
        $params = [$data['name'], $data['email'], $data['role'], $data['phone'] ?? null, $data['is_active'] ?? 1];

        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        Database::get()->prepare($sql)->execute($params);
    }

    public static function delete(int $id): void
    {
        Database::get()->prepare('UPDATE users SET deleted_at = NOW() WHERE id = ?')->execute([$id]);
    }

    public static function toggleActive(int $id): void
    {
        Database::get()->prepare('UPDATE users SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?')->execute([$id]);
    }
}
