<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../database/connection.php';

use App\Services\QueueService;

echo "Worker started!\n";

$queueService = new QueueService();
$queueName = 'email';
const DURATION = 5;

while (true) {
    try {
        $processed = $queueService->processNext($queueName);

        if ($processed) {
            echo "Job completed!\n";
        } else {
            echo "No jobs, sleeping...\n";
            // XXX: Set the delay here
            sleep(DURATION);
        }
    } catch (Throwable $e) {
        echo "Error: {$e->getMessage()}\n";
        sleep(DURATION);
    }
}
