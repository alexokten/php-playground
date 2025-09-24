<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateAttendeeDTO;
use App\DTOs\UpdateAttendeeDTO;
use App\DTOs\AnonymiseAttendeeDTO;
use App\DTOs\GetAttendeeDTO;
use App\DTOs\RegisterForEventDTO;
use App\Helpers\Response;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Carbon;
use RequestItem;

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

class AttendeeController
{
    public static function getAllAttendees(): void
    {
        $attendees = Attendee::all();
        Response::sendSuccess(
            [
                $attendees->toArray()
            ],
            'Attendees retrieved successfully'
        );
    }

    public function getAttendeeById(RequestItem $request): void
    {
        $dto = new GetAttendeeDTO((int)$request->params[':id']);
        $attendeeWithId = Attendee::find($dto->id);
        Response::sendSuccess(
            [
                $attendeeWithId
            ],
            'Attendee retrieved successfully'
        );
    }

    public function createAttendee(RequestItem $request): void
    {
        $dto = CreateAttendeeDTO::create($request);

        if (Attendee::where('email', $dto->email)->exists()) {
            Response::sendError('User already exists', 409);
            return;
        }
        $result = Attendee::create($dto->toArray());
        Response::sendSuccess(
            [
                'sent' => $dto->toArray(),
                'recieved' => $result,
            ],
            'Attendee created successfully'
        );
    }

    public function updateAttendee(RequestItem $request): void
    {
        $dto = UpdateAttendeeDTO::create($request)->toArray();
        $result = Attendee::find($dto['id'], 'id')->update($dto);
        Response::sendSuccess(
            [
                'recieved' => $result,
            ],
            'Attendee updated successfully'
        );
    }

    public function anonymiseAttendee(RequestItem $request): void
    {
        $attendeeId = AnonymiseAttendeeDTO::create($request)->getId();
        $attendee = Attendee::find($attendeeId);

        if (!$attendee) {
            Response::sendError('Attendee not found', Response::HTTP_NOT_FOUND);
        }

        $beforeAnonymisation = [
            'id' => $attendee->id,
            'firstName' => $attendee->firstName,
            'lastName' => $attendee->lastName,
            'email' => $attendee->email,
            'city' => $attendee->city,
            'isActive' => $attendee->isActive,
        ];


        $anonymisedDTO = AnonymiseAttendeeDTO::fromAttendee($attendeeId)->toArray();
        $attendee->update($anonymisedDTO);

        Response::sendSuccess(
            [
                'sent' => $beforeAnonymisation,
                'recieved' => $anonymisedDTO
            ],
            'Attendee anonymised successfully'
        );
    }

    public function getAttendeeEvents(RequestItem $request): void
    {
        $attendeeId = GetAttendeeDTO::getId($request);
        $attendee = Attendee::with('events')->find($attendeeId);
        $events = $attendee->events;
        Response::sendSuccess($events->toArray(), 'Attendee events retrieved successfully');
    }

    public function registerForEvent(RequestItem $request): void
    {
        $registrationDTO = RegisterForEventDTO::create($request);
        $attendeeId = $registrationDTO->getAttendeeId();
        $eventId = $registrationDTO->getEventId();

        $attendee = Attendee::find($attendeeId);
        if (!$attendee) {
            Response::sendError('Attendee not found', 404);
        }

        $event = Event::find($eventId);
        if (!$event) {
            Response::sendError('Event not found', 404);
        }

        $exists = $attendee->activeEvents()->where('eventId', $eventId)->exists();
        if ($exists) {
            Response::sendError('Attendee already registered for event', 400);
        }

        $registrationDetails = $registrationDTO->withRegistered()->toDatabaseArray();

        $attendee->events()->sync([
            $eventId => $registrationDetails
        ], false);

        Response::sendSuccess([
            'attendee_id' => $attendeeId,
            'event_id' => $eventId
        ], 'Successfully registered for event');
    }

    public function unregisterForEvent(RequestItem $request): void
    {
        $unregistrationDTO = RegisterForEventDTO::create($request);
        $attendeeId = $unregistrationDTO->getAttendeeId();
        $eventId = $unregistrationDTO->getEventId();

        $attendee = Attendee::find($attendeeId);
        if (!$attendee) {
            Response::sendError('Attendee not found', 404);
        }

        $event = Event::find($eventId);
        if (!$event) {
            Response::sendError('Event not found', 404);
        }

        $exists = $attendee->activeEvents()->where('eventId', $eventId)->exists();
        if (!$exists) {
            Response::sendError('Attendee is not registered for this event', 404);
        }

        $unregistrationDetails = $unregistrationDTO->withUnregistered()->toDatabaseArray();

        $attendee->events()->sync([
            $eventId => $unregistrationDetails
        ], false);

        Response::sendSuccess([
            'attendee_id' => $attendeeId,
            'event_id' => $eventId
        ], 'Successfully unregistered from event');
    }
}
