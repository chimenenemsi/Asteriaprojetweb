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

    protected function isPositiveInteger(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]) !== false;
    }

    protected function matchesLabelPattern(string $value): bool
    {
        return preg_match("/^[\p{L}\p{N}\s&(),.'\/+\-]+$/u", $value) === 1;
    }
}
