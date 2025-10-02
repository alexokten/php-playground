<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Queue;
use App\Repositories\QueueRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class QueueService
{
    private QueueRepository $queueRepository;

    public function __construct()
    {
        $this->queueRepository = new QueueRepository();
    }

    // Add item to queue in controller
    public function addToQueue(object $job, string $queueName = 'default', int $delaySeconds = 0): mixed
    {
        $payloadJSON = json_encode(
            [
                'class' => get_class($job), // <- get the classname from the $job instance
                'data' => get_object_vars($job) // <- get the data from the $job instance
            ]
        );

        $availableAt = Carbon::now()->addSeconds($delaySeconds); // <- set time to now and add delay

        return $this->queueRepository->push(
            queue: $queueName,
            payload: $payloadJSON,
            availableAt: $availableAt,
        );

        ray('Job dispatched to database');
    }

    // Process next available job in queue
    public function processNext(string $queueName = 'default'): bool
    {
        $job = $this->queueRepository->pop($queueName);

        if (!$job) {
            return false;
        }

        try {
            $payload = json_decode($job->payload, true);
            $jobClass = $payload['class'];
            $jobData = $payload['data'];

            $jobInstance = new $jobClass(...array_values($jobData));
            $jobInstance->handle();

            $this->queueRepository->markAsComplete($job);

            ray("Job {$job->id} completed");

            return true;
        } catch (Throwable $e) {
            if ($job->attempts < 3) {
                $delay = pow(2, $job->attempts) * 60; // <- pow === power, each retry waits twice as long as the prev one
                $this->queueRepository->release($job, delay: $delay);
                ray("Job {$job->id} will retry in {$delay}s")->yellow();
                return false;
            } else {
                $this->queueRepository->markAsFailed($job);
                ray("Job {$job->id} permanently failed")->red();
                return false;
            }
        }
    }

    public function getPendingCount(string $queueName = 'default'): int
    {
        return $this->queueRepository->getAllPendingJobsCount($queueName);
    }

    public function getLastProcessed(string $queueName = 'default'): Queue
    {
        return $this->queueRepository->getLastCompletedJob($queueName);
    }
}
