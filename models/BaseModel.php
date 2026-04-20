<?php
declare(strict_types=1);

abstract class BaseModel
{
    public function __construct(protected PDO $connection)
    {
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
