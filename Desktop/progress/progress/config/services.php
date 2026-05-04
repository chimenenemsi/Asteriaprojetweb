<?php
declare(strict_types=1);

function progress_load_dotenv(string $path): void
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

function progress_env(string $name, string $default = ''): string
{
    $value = getenv($name);
    if ($value === false || trim((string) $value) === '') {
        return $default;
    }

    return trim((string) $value);
}

function progress_services(): array
{
    progress_load_dotenv(ROOT_PATH . '/.env');

    return [
        'gemini_api_key' => progress_env('GEMINI_API_KEY', progress_env('GOOGLE_API_KEY', '')),
        'gemini_model' => progress_env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'gemini_api_url' => progress_env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models'),
    ];
}
