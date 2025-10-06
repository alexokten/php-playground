<?php

declare(strict_types=1);

function parseExplainLine($line)
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
        $parsed['row_estimate_accuracy'] = (string) round(((float) $parsed['actual_rows'] / (float) $parsed['estimated_rows']) * 100.0, 1) . '%';
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

function getPerformanceColor($duration)
{
    if ($duration < 0.001) return 'green';
    if ($duration < 0.01) return 'blue';
    if ($duration < 0.1) return 'orange';
    return 'red';
}

function getOverallPerformanceFromExplain($parsedExplain)
{
    // For MySQL EXPLAIN ANALYZE, the root operation (usually first step) contains the total time
    // Look for the highest-level operation time as it represents the total query execution time
    $totalDuration = 0;

    foreach ($parsedExplain as $step) {
        if (isset($step['actual_time']['end'])) {
            // Use the 'end' time from the root operation as it represents total execution time
            $totalDuration = max($totalDuration, $step['actual_time']['end']);
        }
    }

    // Convert from milliseconds to seconds if needed (MySQL EXPLAIN ANALYZE typically uses milliseconds)
    return $totalDuration / 1000;
}
