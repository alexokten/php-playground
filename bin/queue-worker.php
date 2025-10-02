#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../database/connection.php';

use App\Services\QueueService;

// Configuration
$queueName = 'email';  // Allow queue name as argument
$sleepDuration = 2;

echo "Queue worker started\n";
echo "Queue: {$queueName}\n";
echo "Sleep: {$sleepDuration}s\n\n";

$queueService = new QueueService();

while (true) {
    try {
        $processed = $queueService->processBatch($queueName);

        if ($processed) {
            echo "Job completed (Total: {$processed})\n";
        } else {
            echo "No jobs, sleeping {$sleepDuration}s...\n";
            sleep($sleepDuration);
        }
    } catch (Throwable $e) {
        echo "Error: {$e->getMessage()}\n";
        echo "File: {$e->getFile()}:{$e->getLine()}\n";
        sleep($sleepDuration);
    }
}
