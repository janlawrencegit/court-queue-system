-- Store per-match rental minutes set during queue creation.
SET @db := DATABASE();
SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'rental_minutes'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `rental_minutes` int(11) DEFAULT NULL AFTER `rental_ends_at`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

