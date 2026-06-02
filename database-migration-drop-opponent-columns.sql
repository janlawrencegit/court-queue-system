-- Normalize queue players into players_json and drop split columns.
-- Safe to re-run.
SET @db := DATABASE();

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'players_json'
);
SET @sql := IF(@has_col = 0,
    'ALTER TABLE `queues` ADD COLUMN `players_json` LONGTEXT NULL DEFAULT NULL AFTER `player_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `queues`
SET `players_json` = JSON_ARRAY(
    JSON_OBJECT('id', COALESCE(`player_id`, 0), 'name', COALESCE(`player_name`, '')),
    JSON_OBJECT(
        'id',
        COALESCE(
            `partner_player_id`,
            `player2_id`,
            `opponent_player_id`,
            0
        ),
        'name',
        COALESCE(
            `partner_name`,
            `player2_name`,
            `opponent_name`,
            ''
        )
    ),
    JSON_OBJECT(
        'id',
        COALESCE(
            `player2_id`,
            `opponent_player_id`,
            `player4_id`,
            `opponent_partner_id`,
            0
        ),
        'name',
        COALESCE(
            `player2_name`,
            `opponent_name`,
            `player4_name`,
            `opponent_partner_name`,
            ''
        )
    ),
    JSON_OBJECT(
        'id',
        COALESCE(`player4_id`, `opponent_partner_id`, 0),
        'name',
        COALESCE(`player4_name`, `opponent_partner_name`, '')
    )
)
WHERE (`players_json` IS NULL OR `players_json` = '');

UPDATE `queues`
SET `players_json` = JSON_REMOVE(
    JSON_REMOVE(
        JSON_REMOVE(
            JSON_REMOVE(`players_json`, '$[3]'),
            '$[2]'
        ),
        '$[1]'
    ),
    '$[0]'
)
WHERE `players_json` IS NOT NULL
  AND JSON_VALID(`players_json`) = 1
  AND JSON_TYPE(`players_json`) = 'ARRAY'
  AND JSON_LENGTH(`players_json`) = 4
  AND JSON_EXTRACT(`players_json`, '$[0].name') = ''
  AND JSON_EXTRACT(`players_json`, '$[1].name') = ''
  AND JSON_EXTRACT(`players_json`, '$[2].name') = ''
  AND JSON_EXTRACT(`players_json`, '$[3].name') = '';

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'partner_player_id'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `partner_player_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'player2_id'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `player2_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'player4_id'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `player4_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'partner_name'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `partner_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'player2_name'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `player2_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'player4_name'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `player4_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_player_id'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `opponent_player_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_partner_id'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `opponent_partner_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_name'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `opponent_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'queues' AND COLUMN_NAME = 'opponent_partner_name'
);
SET @sql := IF(@has_col > 0,
    'ALTER TABLE `queues` DROP COLUMN `opponent_partner_name`',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
