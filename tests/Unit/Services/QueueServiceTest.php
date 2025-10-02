<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\QueueStatus;
use App\Jobs\SendEmailJob;
use App\Models\Queue;
use App\Services\QueueService;
use Tests\TestCase;

class QueueServiceTest extends TestCase
{
    private QueueService $queueService;

    public function setUp(): void
    {
        parent::setUp();
        $this->queueService = new QueueService();
    }

    public function test_can_add_job_to_queue(): void
    {
        $job = new SendEmailJob(
            to: 'test@example.com',
            subject: 'test email',
            body: 'hello'
        );

        $this->queueService->addToQueue($job);

        $queuedJob = Queue::where('queue', 'default')->first();
        $this->assertNotNull($queuedJob);
        $this->assertEquals('default', $queuedJob->queue);
        $this->assertEquals(QueueStatus::PENDING, $queuedJob->status);
    }

    public function test_can_add_job_with_custom_queue_name(): void
    {
        $job = new SendEmailJob('user@test.com', 'Subject', 'Body');

        $this->queueService->addToQueue($job, 'emails');

        $queuedJob = Queue::where('queue', 'emails')->first();
        $this->assertNotNull($queuedJob);
        $this->assertEquals('emails', $queuedJob->queue);
    }

    public function test_job_payload_contains_correct_data(): void
    {
        $job = new SendEmailJob(
            to: 'user@example.com',
            subject: 'Test Subject',
            body: 'Test Body'
        );

        $this->queueService->addToQueue($job);

        $queuedJob = Queue::first();
        $payload = json_decode($queuedJob->payload, true);

        $this->assertEquals(SendEmailJob::class, $payload['class']);
        $this->assertEquals('user@example.com', $payload['data']['to']);
        $this->assertEquals('Test Subject', $payload['data']['subject']);
        $this->assertEquals('Test Body', $payload['data']['body']);
    }

    public function test_pending_count_returns_zero_when_queue_empty(): void
    {
        $count = $this->queueService->getPendingCount('default');

        $this->assertEquals(0, $count);
    }

    public function test_pending_count_returns_correct_number(): void
    {
        $job = new SendEmailJob('user@test.com', 'Subject', 'Body');
        $this->queueService->addToQueue($job);
        $this->queueService->addToQueue($job);
        $this->queueService->addToQueue($job);

        $count = $this->queueService->getPendingCount('default');

        $this->assertEquals(3, $count);
    }

    public function test_process_next_returns_false_when_queue_empty(): void
    {
        $result = $this->queueService->processNext();

        $this->assertFalse($result);
    }

    public function test_can_process_job_from_queue(): void
    {
        $job = new SendEmailJob(
            to: 'test@example.com',
            subject: 'test email',
            body: 'hello'
        );

        $response = $this->queueService->addToQueue($job);

        $this->assertNull($response->reservedAt);
        $this->assertEquals(QueueStatus::PENDING, $response->status);

        $this->queueService->processNext();
        $lastProcssedJob = $this->queueService->getLastProcessed();

        $this->assertEquals($response->id, $lastProcssedJob->id);
        $this->assertNotNull($lastProcssedJob->reservedAt);
        $this->assertEquals(QueueStatus::COMPLETED, $lastProcssedJob->status);
    }

    public function test_process_batch_processes_multiple_jobs(): void
    {
        // Arrange - add 5 jobs
        $job = new SendEmailJob('user@test.com', 'Subject', 'Body');
        for ($i = 0; $i < 5; $i++) {
            $this->queueService->addToQueue($job);
        }

        // Act
        $processedCount = $this->queueService->processBatch('default', 10);

        // Assert
        $this->assertEquals(5, $processedCount);
        $completedJobs = Queue::where('status', QueueStatus::COMPLETED)->count();
        $this->assertEquals(5, $completedJobs);
    }

    public function test_process_batch_respects_batch_size(): void
    {
        // Arrange - add 15 jobs
        $job = new SendEmailJob('user@test.com', 'Subject', 'Body');
        for ($i = 0; $i < 15; $i++) {
            $this->queueService->addToQueue($job);
        }

        // Act - process batch of 10
        $processedCount = $this->queueService->processBatch('default', 10);

        // Assert
        $this->assertEquals(10, $processedCount);
        $pendingCount = Queue::where('status', QueueStatus::PENDING)->count();
        $this->assertEquals(5, $pendingCount); // 5 left
    }

    public function test_process_batch_returns_correct_count(): void
    {
        // Arrange
        $job = new SendEmailJob('user@test.com', 'Subject', 'Body');
        $this->queueService->addToQueue($job);
        $this->queueService->addToQueue($job);
        $this->queueService->addToQueue($job);

        // Act
        $count = $this->queueService->processBatch('default', 10);

        // Assert
        $this->assertEquals(3, $count);
    }

    public function test_process_batch_returns_zero_when_queue_empty(): void
    {
        // Act
        $count = $this->queueService->processBatch('default', 10);

        // Assert
        $this->assertEquals(0, $count);
    }

    public function test_process_batch_with_different_queues(): void
    {
        // Arrange - add jobs to different queues
        $job = new SendEmailJob('user@test.com', 'Subject', 'Body');
        $this->queueService->addToQueue($job, 'emails');
        $this->queueService->addToQueue($job, 'emails');
        $this->queueService->addToQueue($job, 'notifications');

        // Act - process only emails queue
        $emailsProcessed = $this->queueService->processBatch('emails', 5);

        // Assert
        $this->assertEquals(2, $emailsProcessed);
        $notificationsPending = Queue::where('queue', 'notifications')
            ->where('status', QueueStatus::PENDING)
            ->count();
        $this->assertEquals(1, $notificationsPending); // notifications queue untouched
    }
}
