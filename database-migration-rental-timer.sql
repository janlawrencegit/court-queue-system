-- Add rental timer support for live display and queue actions.
SET @db := DATABASE();
SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'rental_ends_at'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `rental_ends_at` timestamp NULL DEFAULT NULL AFTER `served_at`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO `settings` (`group`, `key`, `value`, `type`, `created_at`, `updated_at`)
VALUES
  ('display', 'rental_default_minutes', '60', 'integer', NOW(), NOW()),
  ('display', 'rental_extend_minutes', '30', 'integer', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `value` = VALUES(`value`),
  `updated_at` = NOW();
