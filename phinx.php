<?php

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env if it exists (for local dev), skip in CI where env vars are set directly
if (file_exists(__DIR__ . '/.env.local')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load('.env.local');
    ray($dotenv);
} elseif (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
// If no .env files exist (CI environment), use environment variables directly

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeders',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_DATABASE'],
            'user' => $_ENV['DB_USERNAME'],
            'pass' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST_TEST'] ?? 'db_test',
            'name' => $_ENV['DB_DATABASE_TEST'] ?? 'headfirst_db_test',
            'user' => $_ENV['DB_USERNAME_TEST'] ?? 'headfirst_user_test',
            'pass' => $_ENV['DB_PASSWORD_TEST'] ?? 'password_test',
            'port' => $_ENV['DB_PORT'] ?? 3307,
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
        ],
    ],
];
