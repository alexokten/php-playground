<?php

require_once 'init.php';

use Illuminate\Database\Capsule\Manager as DB;

echo "Testing database connection...\n";

try {
    // Test basic connection
    $result = DB::select('SELECT 1 as test');
    echo "✅ Database connection successful!\n";
    
    // Show database info
    $dbName = DB::select('SELECT DATABASE() as db_name')[0]->db_name;
    echo "📊 Connected to database: {$dbName}\n";
    
    // Show tables (if any exist)
    $tables = DB::select('SHOW TABLES');
    if (empty($tables)) {
        echo "📝 No tables found - ready to create some!\n";
    } else {
        echo "📋 Existing tables:\n";
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            echo "   - {$tableName}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "🔧 Make sure your Docker containers are running: docker-compose up -d\n";
}

echo "\nDone!\n";
