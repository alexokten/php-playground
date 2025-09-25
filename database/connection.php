<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Dotenv\Dotenv;

/** 1 - Load environment variables (prefer local if exists) */
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
if (file_exists(__DIR__ . '/../.env.local')) {
    $dotenv->load('.env.local');
} else {
    $dotenv->load();
}

/** 2 - Create Capsule instance */
$capsule = new Capsule;

/** 3 - Add database connection */
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'port' => $_ENV['DB_PORT'] ?? 3306,
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

/** 4 - Set the event dispatcher used by Eloquent models */
$capsule->setEventDispatcher(new Dispatcher(new Container));

/** 5 - Make this Capsule instance available globally via static methods */
$capsule->setAsGlobal();

/** 6 - Setup the Eloquent ORM */
$capsule->bootEloquent();

/** 7 - Setup query logging to Ray */
if (class_exists('Spatie\Ray\Ray')) {
    // Track EXPLAIN ANALYZE queries to avoid logging them
    $isExplainQuery = false;

    $capsule->getConnection()->listen(function ($query) use (&$isExplainQuery) {
        // Skip if this is an EXPLAIN ANALYZE query we triggered
        if (stripos(trim($query->sql), 'EXPLAIN ANALYZE') === 0) {
            return;
        }

        $fullQuery = $query->sql;
        foreach ($query->bindings as $binding) {
            if ($binding === null) {
                $value = 'NULL';
            } elseif (is_bool($binding)) {
                $value = $binding ? '1' : '0';
            } elseif (is_string($binding)) {
                $value = "'" . addslashes($binding) . "'";
            } else {
                $value = (string) $binding;
            }
            $fullQuery = preg_replace('/\?/', $value, $fullQuery, 1);
        }

        $formattedQuery = preg_replace([
            '/\bSELECT\b/i',
            '/\bFROM\b/i',
            '/\b(INNER|LEFT|RIGHT|FULL)\s+JOIN\b/i',
            '/\bWHERE\b/i',
            '/\bAND\b/i',
            '/\bOR\b/i',
            '/\bGROUP\s+BY\b/i',
            '/\bORDER\s+BY\b/i',
            '/\bLIMIT\b/i',
        ], [
            "\nSELECT",
            "\nFROM",
            "\n$1 JOIN",
            "\nWHERE",
            "\n    AND",
            "\n    OR",
            "\nGROUP BY",
            "\nORDER BY",
            "\nLIMIT",
        ], $fullQuery);

        ray()
            ->html('<pre style="font-size: 10px; line-height: 1.4; background: #212936; color: #76DB88; padding: 8px; border-radius: 2px;">' . htmlspecialchars($formattedQuery) . '</pre>')
            ->label('SQL Query (' . $query->time . 'ms)')
            ->orange();

        // Only run EXPLAIN ANALYZE for SELECT queries
        if (stripos(trim($query->sql), 'SELECT') === 0) {
            try {
                // Set flag to prevent logging the EXPLAIN query
                $isExplainQuery = true;

                $explainResults = $query->connection->select("EXPLAIN ANALYZE " . $query->sql, $query->bindings);

                $parsedExplain = [];
                $stepNumber = 1;

                foreach ($explainResults as $row) {
                    $explainColumn = $row->EXPLAIN ?? $row->{'QUERY PLAN'} ?? $row->explain ?? null;
                    if ($explainColumn) {
                        $parsed = parseExplainLine($explainColumn, $stepNumber);
                        if ($parsed) {
                            $parsedExplain["Step {$stepNumber}"] = $parsed;
                            $stepNumber++;
                        }
                    }
                }

                if (!empty($parsedExplain)) {
                    ray($parsedExplain)
                        ->label('SQL Performance')
                        ->red()
                        ->expand();
                }

                // Reset flag
                $isExplainQuery = false;
            } catch (\Exception $e) {
                $isExplainQuery = false; // Reset flag on error too
                ray()
                    ->label('Explain Analyze Error')
                    ->yellow()
                    ->text('Could not execute EXPLAIN ANALYZE: ' . $e->getMessage());
            }
        }
    });
}

function parseExplainLine($line, $stepNumber)
{
    // Remove leading/trailing whitespace and arrows
    $line = trim($line, "-> \t\n\r\0\x0B");

    if (empty($line)) {
        return null;
    }

    $parsed = [
        'operation' => '',
        'cost_estimate' => null,
        'estimated_rows' => null,
        'actual_time' => null,
        'actual_rows' => null,
        'loops' => null,
        'details' => '',
        'raw_line' => $line
    ];

    // Extract operation name (everything before the first parenthesis)
    if (preg_match('/^([^(]+)/', $line, $matches)) {
        $parsed['operation'] = trim($matches[1]);
    }

    // Extract cost estimate
    if (preg_match('/cost=([0-9.]+)/', $line, $matches)) {
        $parsed['cost_estimate'] = (float)$matches[1];
    }

    // Extract estimated rows
    if (preg_match('/rows=([0-9]+)/', $line, $matches)) {
        $parsed['estimated_rows'] = (int)$matches[1];
    }

    // Extract actual time
    if (preg_match('/actual time=([0-9.]+)\.\.([0-9.]+)/', $line, $matches)) {
        $parsed['actual_time'] = [
            'start' => (float)$matches[1],
            'end' => (float)$matches[2],
            'duration' => (float)$matches[2] - (float)$matches[1]
        ];
    }

    // Extract actual rows
    if (preg_match('/actual.*rows=([0-9]+)/', $line, $matches)) {
        $parsed['actual_rows'] = (int)$matches[1];
    }

    // Extract loops
    if (preg_match('/loops=([0-9]+)/', $line, $matches)) {
        $parsed['loops'] = (int)$matches[1];
    }

    // Extract details (table names, conditions, etc.)
    if (preg_match('/on (\w+)/', $line, $matches)) {
        $parsed['table'] = $matches[1];
    }

    if (preg_match('/using (\w+)/', $line, $matches)) {
        $parsed['index'] = $matches[1];
    }

    // Calculate efficiency metrics
    if ($parsed['estimated_rows'] && $parsed['actual_rows']) {
        $parsed['row_estimate_accuracy'] = round(($parsed['actual_rows'] / $parsed['estimated_rows']) * 100, 1) . '%';
    }

    if (isset($parsed['actual_time']['duration'])) {
        $parsed['performance_rating'] = getPerformanceRating($parsed['actual_time']['duration']);
    }

    return $parsed;
}

function getPerformanceRating($duration)
{
    if ($duration < 0.001) return 'Excellent';
    if ($duration < 0.01) return 'Good';
    if ($duration < 0.1) return 'Fair';
    return 'Slow';
}
