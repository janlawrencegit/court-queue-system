-- Remove court_name safely (court_number is the only label).
SET @db := DATABASE();
SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'courts' AND COLUMN_NAME = 'court_name'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `courts` DROP COLUMN `court_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
