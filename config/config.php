<?php

declare(strict_types=1);

// Если переменная уже задана в $_ENV (как из docker-compose), её не перезаписываем
$envFile = dirname(__DIR__) . '/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (!array_key_exists($key, $_ENV) && getenv($key) === false) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

$env = static fn(string $key, string $default = ''): string =>
    $_ENV[$key] ?? (getenv($key) !== false ? (string)getenv($key) : $default);

return [
    'db' => [
        'host' => $env('DB_HOST', 'mysql'),
        'port' => (int) $env('DB_PORT', '3306'),
        'name' => $env('DB_NAME', 'blog'),
        'user' => $env('DB_USER', 'blog'),
        'pass' => $env('DB_PASS', 'blog'),
    ],
    'app' => [
        'env'                => $env('APP_ENV', 'production'),
        'url'                => $env('APP_URL', ''),
        'articles_per_page'  => (int) $env('ARTICLES_PER_PAGE', '6'),
        'similar_articles'   => 3,
    ],
];
