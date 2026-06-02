<?php

declare(strict_types=1);

class Database
{
    /** @var PDO|null */
    private static $pdo = null;
    /** @var string|null */
    private static $connectedHost = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            [$pdo, $host] = self::connect();
            self::$pdo = $pdo;
            self::$connectedHost = $host;
        }
        return self::$pdo;
    }

    public static function connectedHost(): ?string
    {
        return self::$connectedHost;
    }

    /**
     * Try primary host from .env, then common shared-hosting fallbacks.
     *
     * @return array{0: PDO, 1: string}
     */
    public static function connect(): array
    {
        $name = config('db.name');
        $user = config('db.user');
        $pass = config('db.pass');
        $charset = config('db.charset', 'utf8mb4');
        $port = (int) config('db.port', 3306);

        $primary = config('db.host');
        $hosts = array_values(array_unique(array_filter([
            $primary,
            // InfinityFree / iFastNet: sometimes localhost works when sql*.infinityfree.com does not (or vice versa)
            $primary !== 'localhost' ? 'localhost' : null,
            $primary !== '127.0.0.1' ? '127.0.0.1' : null,
        ])));

        $errors = [];

        foreach ($hosts as $host) {
            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 10,
                ]);
                self::applySessionTimezone($pdo);
                return [$pdo, $host];
            } catch (PDOException $e) {
                $errors[$host] = $e->getMessage();
            }
        }

        $msg = "Could not connect to MySQL. Tried: " . implode(', ', $hosts) . ". ";
        $msg .= "Errors: " . json_encode($errors);
        throw new PDOException($msg);
    }

    private static function applySessionTimezone(PDO $pdo): void
    {
        try {
            $tz = new DateTimeZone((string) config('timezone', 'Asia/Manila'));
            $offset = (new DateTime('now', $tz))->format('P');
            $pdo->exec('SET time_zone = ' . $pdo->quote($offset));
        } catch (Throwable $e) {
            // Keep default server timezone if setting fails.
        }
    }

    /** @return array{status: string, user: ?array, error: ?string, host: ?string, config_host: string} */
    public static function testConnection(): array
    {
        $configHost = (string) config('db.host');

        try {
            [$pdo, $host] = self::connect();
            self::$pdo = $pdo;
            self::$connectedHost = $host;
            $stmt = $pdo->query(
                'SELECT id, name, email, role, is_active FROM users WHERE deleted_at IS NULL ORDER BY id ASC LIMIT 1'
            );
            $user = $stmt->fetch() ?: null;

            return [
                'status' => $user ? 'connected' : 'connected_empty',
                'user' => $user ?: null,
                'error' => null,
                'host' => $host,
                'config_host' => $configHost,
            ];
        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'user' => null,
                'error' => $e->getMessage(),
                'host' => null,
                'config_host' => $configHost,
            ];
        }
    }
}
