<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Event\GetSoldTicketsPercentageDTO;
use App\Models\Event;
use App\Repositories\EventRepository;
use Brick\JsonMapper\JsonMapper;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class EventService
{
    private readonly EventRepository $eventRepository;

    public function __construct()
    {
        $this->eventRepository = new EventRepository();
    }

    public function getAllEvents(): Collection
    {
        return $this->eventRepository->findAll();
    }

    public function getEventTicketSalesOfMaxTickets(GetSoldTicketsPercentageDTO $eventId): float | null
    {
        $event = $this->eventRepository->findById($eventId->id);

        if (!$event) {
            throw new Exception('Event does not exist');
        }

        $soldTickets = $this->eventRepository->countSoldTickets($event);
        $maxTickets = $event->maxTickets;
        $percentageSold = $soldTickets / $maxTickets * 100;
        return $percentageSold;
    }
}
