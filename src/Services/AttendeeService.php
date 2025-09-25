<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateAttendeeDTO;
use App\DTOs\UpdateAttendeeDTO;
use App\DTOs\RegisterForEventDTO;
use App\DTOs\AnonymiseAttendeeDTO;
use App\Models\Attendee;
use App\Models\Event;
use App\Repositories\AttendeeRepository;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use RuntimeException;

class AttendeeService
{
    public function __construct(
        private readonly AttendeeRepository $attendeeRepository
    ) {}

    public function getAllAttendees(): Collection
    {
        return $this->attendeeRepository->findAll();
    }

    public function getAttendeeById(int $id): Attendee
    {
        $attendee = $this->attendeeRepository->findById($id);

        if (!$attendee) {
            throw new InvalidArgumentException("Attendee with ID {$id} not found");
        }

        return $attendee;
    }

    public function createAttendee(CreateAttendeeDTO $dto): array
    {
        if ($this->attendeeRepository->emailExists($dto->email)) {
            throw new RuntimeException('User already exists');
        }

        $attendee = $this->attendeeRepository->create($dto->toDatabaseArray());

        return [
            'sent' => $dto->toArray(),
            'received' => $attendee->toArray()
        ];
    }

    public function updateAttendee(UpdateAttendeeDTO $dto): array
    {
        $attendee = $this->getAttendeeById($dto->id);

        $this->attendeeRepository->update($attendee, $dto->toArray());
        $attendee->refresh();

        return [
            'received' => $attendee->toArray()
        ];
    }

    public function getAttendeeEvents(int $attendeeId): array
    {
        $attendee = $this->attendeeRepository->findWithEvents($attendeeId);

        if (!$attendee) {
            throw new InvalidArgumentException("Attendee with ID {$attendeeId} not found");
        }

        return $attendee->events->toArray();
    }

    public function anonymizeAttendee(int $attendeeId): array
    {
        $attendee = $this->getAttendeeById($attendeeId);

        $beforeAnonymization = [
            'id' => $attendee->id,
            'firstName' => $attendee->firstName,
            'lastName' => $attendee->lastName,
            'email' => $attendee->email,
            'city' => $attendee->city,
            'isActive' => $attendee->isActive,
        ];

        $anonymizedDTO = AnonymiseAttendeeDTO::fromAttendee($attendeeId);
        $this->attendeeRepository->update($attendee, $anonymizedDTO->toArray());

        return [
            'sent' => $beforeAnonymization,
            'received' => $anonymizedDTO->toArray()
        ];
    }

    public function registerForEvent(RegisterForEventDTO $dto): array
    {
        $attendeeId = $dto->getAttendeeId();
        $eventId = $dto->getEventId();

        $this->getAttendeeById($attendeeId);

        $event = Event::find($eventId);
        if (!$event) {
            throw new InvalidArgumentException("Event with ID {$eventId} not found");
        }

        if ($this->attendeeRepository->isRegisteredForEvent($attendeeId, $eventId)) {
            throw new RuntimeException('Attendee already registered for event');
        }

        $registrationData = $dto->withRegistered()->toDatabaseArray();
        $this->attendeeRepository->registerForEvent($attendeeId, $eventId, $registrationData);

        return [
            'attendee_id' => $attendeeId,
            'event_id' => $eventId
        ];
    }

    public function unregisterFromEvent(RegisterForEventDTO $dto): array
    {
        $attendeeId = $dto->getAttendeeId();
        $eventId = $dto->getEventId();

        $this->getAttendeeById($attendeeId);

        $event = Event::find($eventId);
        if (!$event) {
            throw new InvalidArgumentException("Event with ID {$eventId} not found");
        }

        if (!$this->attendeeRepository->isRegisteredForEvent($attendeeId, $eventId)) {
            throw new RuntimeException('Attendee is not registered for this event');
        }

        $unregistrationData = $dto->withUnregistered()->toDatabaseArray();
        $this->attendeeRepository->unregisterFromEvent($attendeeId, $eventId, $unregistrationData);

        return [
            'attendee_id' => $attendeeId,
            'event_id' => $eventId
        ];
    }
}
