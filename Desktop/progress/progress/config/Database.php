<?php
declare(strict_types=1);

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

    private static function ensureSchema(PDO $pdo): void
    {
        if (!self::tableExists($pdo, 'progress_goals')) {
            $sql = file_get_contents(ROOT_PATH . '/config/schema.sql');
            if ($sql === false) {
                throw new RuntimeException('Unable to load progress schema.');
            }

            $statements = array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: []));
            foreach ($statements as $statement) {
                if ($statement !== '') {
                    $pdo->exec($statement);
                }
            }

            return;
        }

        self::ensureProgressRecordsTable($pdo);
    }

    private static function tableExists(PDO $pdo, string $tableName): bool
    {
        $stmt = $pdo->query(sprintf('SHOW TABLES LIKE %s', $pdo->quote($tableName)));

        return $stmt->fetch() !== false;
    }

    private static function ensureProgressRecordsTable(PDO $pdo): void
    {
        if (!self::tableExists($pdo, 'progress_records')) {
            $pdo->exec(
                "CREATE TABLE progress_records ("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "progress_goal_id INT NOT NULL,"
                . "record_date DATE NOT NULL,"
                . "recorded_value DECIMAL(10,2) NOT NULL,"
                . "adherence_score INT DEFAULT NULL,"
                . "mood ENUM('LOW', 'STEADY', 'HIGH') DEFAULT 'STEADY',"
                . "record_type ENUM('CHECKPOINT', 'MILESTONE', 'MEASUREMENT', 'NOTE', 'REPORT') DEFAULT 'CHECKPOINT',"
                . "notes TEXT,"
                . "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
                . "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
                . "CONSTRAINT fk_progress_record_goal FOREIGN KEY (progress_goal_id) REFERENCES progress_goals(id) ON DELETE CASCADE"
                . ")"
            );
            return;
        }

        $columns = $pdo->query("SHOW COLUMNS FROM progress_records")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('adherence_score', $columns, true)) {
            $pdo->exec("ALTER TABLE progress_records ADD COLUMN adherence_score INT DEFAULT NULL AFTER recorded_value");
        }
        if (!in_array('mood', $columns, true)) {
            $pdo->exec("ALTER TABLE progress_records ADD COLUMN mood ENUM('LOW', 'STEADY', 'HIGH') DEFAULT 'STEADY' AFTER adherence_score");
        }
        if (!in_array('record_type', $columns, true)) {
            $pdo->exec("ALTER TABLE progress_records ADD COLUMN record_type ENUM('CHECKPOINT', 'MILESTONE', 'MEASUREMENT', 'NOTE', 'REPORT') DEFAULT 'CHECKPOINT' AFTER mood");
        }

        $goalColumns = $pdo->query("SHOW COLUMNS FROM progress_goals")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('goal_type', $goalColumns, true)) {
            $pdo->exec("ALTER TABLE progress_goals ADD COLUMN goal_type ENUM('WEIGHT_LOSS', 'FITNESS', 'CAREER', 'FINANCE', 'LEARNING', 'HEALTH', 'PRODUCTIVITY', 'OTHER') DEFAULT 'OTHER' AFTER target_date");
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
