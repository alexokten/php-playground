<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Queue\GetEmailDelayQueueDTO;
use App\Helpers\ExceptionHandler;
use App\Helpers\Response;
use App\Jobs\SendEmailJob;
use App\Repositories\QueueRepository;
use App\Services\QueueService;
use Brick\JsonMapper\JsonMapper;
use RequestItem;
use Throwable;

class QueueController
{

    private readonly QueueService $queueService;

    public function __construct()
    {
        $this->queueService = new QueueService();
    }

    public function sendTestEmail(RequestItem $request)
    {
        try {
            $delay = new GetEmailDelayQueueDTO((int)$request->params[':delay'])->getDelayInt();
            $job = new SendEmailJob('email@example.com', 'Subject', 'Hello!');
            $this->queueService->addToQueue($job, 'email', $delay ?? 0);
            Response::sendSuccess(
                ["details" => $job, 'seconds_delay' => $delay,]
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    // INFO: Use case of delay 
    public function handleNewSignup(mixed $user): void
    {
        $this->queueService->addToQueue(
            new SendEmailJob($user->email, 'Welcome!', 'Thanks for siging up'),
            'email',
            delaySeconds: 0
        );

        // 24 hours later
        $this->queueService->addToQueue(
            new SendEmailJob($user->email, 'Nudge again', 'Here are some tips...'),
            'email',
            delaySeconds: 86400
        );

        // 7 days later
        $this->queueService->addToQueue(
            new SendEmailJob($user->email, 'Nudge nudge', 'Need help?'),
            'email',
            delaySeconds: 604800
        );
    }
}
