<?php

declare(strict_types=1);

/** @deprecated Use settings_all(), setting(), setting_save() in helpers.php */
class Setting
{
    public static function all(): array
    {
        return settings_all();
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return setting($key, $default);
    }

    public static function set(string $key, string $value, string $group = 'general', string $type = 'string'): void
    {
        setting_save($key, $value, $group, $type);
    }

    public static function clearCache(): void
    {
        settings_clear_cache();
    }
}
