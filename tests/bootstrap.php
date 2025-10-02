<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env if it exists (for local dev), skip in CI
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Default to 127.0.0.1 for CI, or db_test for local Docker
$testHost = $_ENV['DB_HOST_TEST'] ?? (getenv('CI') ? '127.0.0.1' : 'db_test');
$testDatabase = $_ENV['DB_DATABASE_TEST'] ?? 'headfirst_db_test';
$testUsername = $_ENV['DB_USERNAME_TEST'] ?? 'headfirst_user_test';
$testPassword = $_ENV['DB_PASSWORD_TEST'] ?? 'password_test';
$testRootPassword = $_ENV['DB_ROOT_PASSWORD_TEST'] ?? 'root_password_test';

$_ENV['DB_HOST'] = $testHost;
$_ENV['DB_DATABASE'] = $testDatabase;
$_ENV['DB_USERNAME'] = $testUsername;
$_ENV['DB_PASSWORD'] = $testPassword;
$_SERVER['DB_HOST'] = $testHost;
$_SERVER['DB_DATABASE'] = $testDatabase;
$_SERVER['DB_USERNAME'] = $testUsername;
$_SERVER['DB_PASSWORD'] = $testPassword;

$host = $testHost;
$rootPassword = $testRootPassword;

try {
    $pdo = new PDO("mysql:host={$host}", 'root', $rootPassword);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$testDatabase}");
    $regularUser = $_ENV['DB_USERNAME'] ?? 'headfirst_user';
    $pdo->exec("GRANT ALL PRIVILEGES ON {$testDatabase}.* TO '{$regularUser}'@'%'");
} catch (PDOException $e) {
    echo "Failed to create test database: " . $e->getMessage() . "\n";
    exit(1);
}

require_once __DIR__ . '/../database/connection.php';
