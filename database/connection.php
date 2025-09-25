<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils/SqlHighlighter.php';
require_once __DIR__ . '/utils/ExplainAnalyzer.php';

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

        // Format SQL with proper line breaks
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

        // Apply clean SQL syntax highlighting
        $highlightedQuery = highlightSQLClean($formattedQuery);

        // Only run EXPLAIN ANALYZE for SELECT queries to get concrete performance data
        if (stripos(trim($query->sql), 'SELECT') === 0) {
            try {
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
                    // Get concrete performance from EXPLAIN ANALYZE results
                    $explainDuration = getOverallPerformanceFromExplain($parsedExplain);
                    $performanceColor = getPerformanceColor($explainDuration);

                    // Display SQL query with EXPLAIN ANALYZE-based performance color
                    ray()
                        ->html($highlightedQuery)
                        ->label('SQL Query (' . round($explainDuration * 1000, 2) . 'ms from EXPLAIN)')
                        ->{$performanceColor}();

                    ray($parsedExplain)
                        ->label('SQL Performance')
                        ->{$performanceColor}()
                        ->expand();
                } else {
                    // Fallback to Laravel query time if no EXPLAIN data
                    $queryDuration = $query->time / 1000;
                    $performanceColor = getPerformanceColor($queryDuration);
                    
                    ray()
                        ->html($highlightedQuery)
                        ->label('SQL Query (' . $query->time . 'ms)')
                        ->{$performanceColor}();
                }

                $isExplainQuery = false;
            } catch (\Exception $e) {
                $isExplainQuery = false;
                
                // Fallback to Laravel query time if EXPLAIN ANALYZE fails
                $queryDuration = $query->time / 1000;
                $performanceColor = getPerformanceColor($queryDuration);
                
                ray()
                    ->html($highlightedQuery)
                    ->label('SQL Query (' . $query->time . 'ms)')
                    ->{$performanceColor}();

                ray()
                    ->label('Explain Analyze Error')
                    ->yellow()
                    ->text('Could not execute EXPLAIN ANALYZE: ' . $e->getMessage());
            }
        } else {
            // For non-SELECT queries, use Laravel query time
            $queryDuration = $query->time / 1000;
            $performanceColor = getPerformanceColor($queryDuration);
            
            ray()
                ->html($highlightedQuery)
                ->label('SQL Query (' . $query->time . 'ms)')
                ->{$performanceColor}();
        }
    });
}
