<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\ExceptionHandler;
use App\Repositories\QueueRepository;
use Carbon\Carbon;
use Throwable;

class QueueService
{
    private QueueRepository $queueRepository;

    public function __construct()
    {
        $this->queueRepository = new QueueRepository();
    }

    public function dispatch(object $job, string $queueName = 'default', int $delaySeconds = 0): void
    {
        $payloadJSON = json_encode(
            [
                'class' => get_class($job),
                'data' => get_object_vars($job)
            ]
        );

        $availableAt = Carbon::now()->addSeconds($delaySeconds);

        $this->queueRepository->push(
            queue: $queueName,
            payload: $payloadJSON,
            availableAt: $availableAt,
        );

        ray('Job dispatched to database');
    }

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
                $this->queueRepository->release($job, delay: 60);
            } else {
                $this->queueRepository->markAsFailed($job);
            }
            throw $e;
        }
    }

    public function getPendingCount(string $queueName = 'default'): int
    {
        return $this->queueRepository->getAllPendingJobsCount($queueName);
    }
}
