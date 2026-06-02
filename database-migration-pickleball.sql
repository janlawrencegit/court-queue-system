-- Pickleball features: skill level, singles/doubles, partners
-- Run once in phpMyAdmin
SET @db := DATABASE();

SET @has_col := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'players' AND COLUMN_NAME = 'skill_level'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `players` ADD COLUMN `skill_level` varchar(20) NOT NULL DEFAULT ''intermediate'' AFTER `player_code`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'match_type'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `match_type` enum(''singles'',''doubles'') NOT NULL DEFAULT ''singles'' AFTER `party_size`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'partner_player_id'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `partner_player_id` bigint(20) UNSIGNED NULL DEFAULT NULL AFTER `player_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'partner_name'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `partner_name` varchar(100) NULL DEFAULT NULL AFTER `player_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_partner_id'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `opponent_partner_id` bigint(20) UNSIGNED NULL DEFAULT NULL AFTER `opponent_player_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_partner_name'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `opponent_partner_name` varchar(100) NULL DEFAULT NULL AFTER `opponent_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
