<?php
declare(strict_types=1);

namespace Config;

use PDO;
use PDOStatement;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        self::loadDotenv(ROOT_PATH . '/.env');

        $host = self::env('ASTERIA_DB_HOST', '127.0.0.1');
        $port = (int) self::env('ASTERIA_DB_PORT', '3306');
        $database = self::env('ASTERIA_DB_NAME', 'asteria');
        $username = self::env('ASTERIA_DB_USER', 'root');
        $password = self::env('ASTERIA_DB_PASSWORD', '');

        $admin = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $admin->exec(
            sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                str_replace('`', '``', $database)
            )
        );

        self::$connection = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $database),
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        self::ensureSchema(self::$connection);

        return self::$connection;
    }

    /**
     * Alias for connection() to maintain compatibility with diet management module
     */
    public static function getConnexion(): PDO
    {
        return self::connection();
    }

    private static function ensureSchema(PDO $pdo): void
    {
        $stmt = $pdo->query("SHOW TABLES LIKE 'diet_plans'");
        if ($stmt->fetch() !== false) {
            return;
        }

        $sql = file_get_contents(ROOT_PATH . '/config/schema.sql');
        if ($sql === false) {
            throw new RuntimeException('Unable to load produits schema.');
        }

        $statements = array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: []));
        foreach ($statements as $statement) {
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }
    }

    private static function loadDotenv(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));
            if ($name === '') {
                continue;
            }

            $value = trim($value, "\"'");
            if (getenv($name) === false) {
                putenv($name . '=' . $value);
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    private static function env(string $name, string $fallback): string
    {
        $value = getenv($name);

        return $value === false ? $fallback : $value;
    }
}
