<?php
declare(strict_types=1);

abstract class BaseModel
{
    protected PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function connection(): PDO
    {
        return $this->db;
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function clean(mixed $value): string
    {
        return trim((string) $value);
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = $this->clean($value);

        return $value === '' ? null : $value;
    }

    protected function isDateString(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value;
    }
}
