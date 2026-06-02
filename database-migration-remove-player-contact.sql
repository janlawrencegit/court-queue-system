-- Remove contact_number and email from players (run once in phpMyAdmin)
SET @db := DATABASE();

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'players' AND COLUMN_NAME = 'contact_number'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `players` DROP COLUMN `contact_number`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'players' AND COLUMN_NAME = 'email'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `players` DROP COLUMN `email`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
