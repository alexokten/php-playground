<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Support\Carbon;
use Router\RequestItem;

class RegisterForEventDTO
{
    public function __construct(
        public readonly int $attendeeId,
        public readonly int $eventId,
        public readonly ?Carbon $registeredAt = null,
        public readonly ?Carbon $unregisteredAt = null,
    ) {}

    public static function create(RequestItem $request): self
    {
        $data = json_decode($request->body, true);
        return new self(
            attendeeId: $data['attendeeId'],
            eventId: $data['eventId'],
        );
    }

    public function withRegistered(): self
    {
        return new self(
            attendeeId: $this->attendeeId,
            eventId: $this->eventId,
            registeredAt: Carbon::now(),
            unregisteredAt: null,
        );
    }

    public function toDatabaseArray(): array
    {
        return [
            'registeredAt' => $this->registeredAt,
            'unregisteredAt' => $this->unregisteredAt,
        ];
    }

    public function withUnregistered(): self
    {
        return new self(
            attendeeId: $this->attendeeId,
            eventId: $this->eventId,
            registeredAt: null,
            unregisteredAt: Carbon::now(),
        );
    }

    public function getAttendeeId(): int
    {
        return $this->attendeeId;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }
}
