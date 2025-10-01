<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\QueueStatus;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Collection;

class QueueRepository
{
    public function push(
        string $queue,
        string $payload,
        Carbon $availableAt
    ) {
        return Queue::create([
            'queue' => $queue,
            'payload' => $payload,
            'status' => QueueStatus::PENDING,
            'attempts' => 0,
            'availableAt' => $availableAt ?? Carbon::now(),
        ]);
    }

    public function pop(string $queueName = 'default'): Queue | null
    {
        return DB::transaction(function () use ($queueName) {
            $queueJob = Queue::where('queue', $queueName)
                ->where('status', QueueStatus::PENDING)
                ->whereNull('reservedAt')
                ->where('availableAt', '<=', Carbon::now())
                ->orderBy('availableAt', 'asc')
                ->lockForUpdate()
                ->first();

            if ($queueJob) {
                $queueJob->status = QueueStatus::PROCESSING;
                $queueJob->reservedAt = Carbon::now();
                $queueJob->attempts++;
                $queueJob->save();
            }
            return $queueJob;
        });
    }

    public function markAsComplete(Queue $job): void
    {
        $job->status = QueueStatus::COMPLETED;
        $job->completedAt = Carbon::now();
        $job->save();
    }

    public function markAsFailed(Queue $job): void
    {
        $job->status = QueueStatus::FAILED;
        $job->completedAt = Carbon::now();
        $job->save();
    }

    public function release(Queue $job, int $delay = 0): void
    {
        $job->status = QueueStatus::PENDING;
        $job->reservedAt = null;
        $job->availableAt = Carbon::now()->addSeconds($delay);
        $job->save();
    }

    public function getAllFailedJobs(string $queueName = 'default'): Collection
    {
        return Queue::where('queue', $queueName)
            ->where('status', QueueStatus::FAILED)
            ->orderBy('completedAt', 'desc')
            ->get();
    }

    public function getAllPendingJobs(string $queueName = 'default'): Collection
    {
        return Queue::where('queue', $queueName)
            ->where('status', QueueStatus::PENDING)
            ->orderBy('availableAt', 'asc')
            ->get();
    }

    public function getAllPendingJobsCount(string $queueName = 'default'): int
    {
        return Queue::where('queue', $queueName)
            ->where('status', QueueStatus::PENDING)
            ->count();
    }
}
