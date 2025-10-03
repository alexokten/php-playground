<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Event\GetSoldTicketsPercentageDTO;
use App\Services\EventService;
use Brick\JsonMapper\JsonMapper;
use App\Helpers\ExceptionHandler;
use App\Helpers\Response;
use ResponseItem;
use Router\RequestItem;
use Throwable;

class EventController
{
    private readonly EventService $eventService;
    private readonly JsonMapper $jsonMapper;

    public function __construct()
    {
        $this->eventService = new EventService();
        $this->jsonMapper = new JsonMapper();
    }

    public function getAllEvents(): void
    {
        try {
            $events = $this->eventService->getAllEvents();
            Response::sendSuccess(
                $events->toArray(),
                'Attendees retrieved successfully'
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getEventTicketSalesAsPercentage(RequestItem $request): void
    {
        try {
            $eventId = $request->params[':id'];
            $dto = new GetSoldTicketsPercentageDTO((int)$eventId);
            $percentageSold = $this->eventService->getEventTicketSalesOfMaxTickets($dto);

            Response::sendSuccess(
                ['percentageSold' => $percentageSold],
                'Percentage sold retrieved successfully'
            );
        } catch (Throwable $e) {
            ray('caught');
            ExceptionHandler::handle($e);
        }
    }
}
