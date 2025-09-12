<?php

declare(strict_types=1);

// Simple database setup using database connection
require_once __DIR__ . '/connection.php';

use Illuminate\Database\Capsule\Manager as Capsule;

echo "Running database setup...\n";

// Run migrations
$migrationSql = file_get_contents(__DIR__ . '/migrations.sql');

// Remove comments and split properly
$migrationSql = preg_replace('/^--.*$/m', '', $migrationSql);
$statements = array_filter(
    array_map('trim', preg_split('/;\s*$/m', $migrationSql)),
    function ($stmt) {
        return !empty($stmt);
    }
);

foreach ($statements as $statement) {
    if (!empty(trim($statement))) {
        echo "Executing: " . substr(trim($statement), 0, 50) . "...\n";
        Capsule::statement($statement);
    }
}

echo "Tables created\n";

// Run seeders  
$seederSql = file_get_contents(__DIR__ . '/seeders.sql');

// Remove comments and split properly
$seederSql = preg_replace('/^--.*$/m', '', $seederSql);
$statements = array_filter(
    array_map('trim', preg_split('/;\s*$/m', $seederSql)),
    function ($stmt) {
        return !empty($stmt);
    }
);

foreach ($statements as $statement) {
    if (!empty(trim($statement))) {
        echo "Seeding: " . substr(trim($statement), 0, 30) . "...\n";
        Capsule::statement($statement);
    }
}

echo "🌱 Data seeded\n";

// Show summary
$userCount = Capsule::table('users')->count();
$eventCount = Capsule::table('events')->count();

echo "✅ Complete! Users: {$userCount}, Events: {$eventCount}\n";
