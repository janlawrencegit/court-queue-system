-- Add opponent fields for backward compatibility (safe to re-run).
SET @db := DATABASE();

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_player_id'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `opponent_player_id` bigint(20) UNSIGNED NULL DEFAULT NULL AFTER `player_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_name'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `opponent_name` varchar(100) NULL DEFAULT NULL AFTER `player_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_idx := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND INDEX_NAME = 'queues_opponent_player_id_index'
);
SET @sql := IF(@has_idx = 0,
    'ALTER TABLE `queues` ADD KEY `queues_opponent_player_id_index` (`opponent_player_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
