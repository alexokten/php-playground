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
}
