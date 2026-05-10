<?php
declare(strict_types=1);

function env(string $name, string $default = ''): string
{
    $value = getenv($name);
    if ($value === false || trim((string) $value) === '') {
        return $default;
    }

    return trim((string) $value);
}

function load_dotenv(string $path): void
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

function services(): array
{
    load_dotenv(ROOT_PATH . '/.env');

    return [
        'gemini_api_key' => env('GEMINI_API_KEY', env('GOOGLE_API_KEY', '')),
        'gemini_model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'gemini_api_url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models'),
        'exercise_api_key' => env('EXERCISE_API_KEY', env('API_NINJAS_API_KEY', '')),
        'exercise_api_url' => env('EXERCISE_API_URL', 'https://api.api-ninjas.com/v1/exercises'),
        'pdf_output_dir' => ROOT_PATH . '/storage/generated-pdfs',
    ];
}

// Aliases for compatibility
function produits_services() { return services(); }
function coaching_services() { return services(); }

return services();
