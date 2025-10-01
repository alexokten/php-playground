<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\ExceptionHandler;
use App\Helpers\Response;
use App\Jobs\SendEmailJob;
use App\Repositories\QueueRepository;
use App\Services\QueueService;
use Brick\JsonMapper\JsonMapper;
use Throwable;

class QueueController
{

    private readonly QueueService $queueService;

    public function __construct()
    {
        $this->queueService = new QueueService();
    }

    public function SendTestEmail()
    {
        try {
            $job = new SendEmailJob('email@example.com', 'Subject', 'Hello!');
            $this->queueService->dispatch($job, 'email', 0);
            Response::sendSuccess(
                ['Test email sent']
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }
}
