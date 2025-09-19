<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\GetAttendeeDTO;
use App\Helpers\Response;
use App\Models\Attendee;
use App\Models\Event;
use Exception;
use Illuminate\Support\Carbon;
use RequestItem;

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

class AttendeeController
{
    public static function getAllAttendees(): void
    {
        $attendees = Attendee::all();
        Response::sendSuccess($attendees->toArray(), 'Attendees retrieved successfully');
    }

    public function getAttendeeById(RequestItem $request): void
    {
        $dto = new GetAttendeeDTO(attendeeId: (int)$request->params[':id']);
        $attendeeWithId = Attendee::find($dto->attendeeId);
        Response::sendSuccess([$attendeeWithId], 'Attendee retrieved successfully');
    }

    public function createAttendee(): array
    {
        $uuid = bin2hex(random_bytes((16)));
        $attendee = Attendee::create([
            'firstName' => 'Jeff',
            'lastName' => 'Hat',
            'email' => 'example' . $uuid . 'example.com',
            'dateOfBirth' => Carbon::createFromFormat('d/m/Y', '19/10/2020'),
            'city' => 'Bristol',
            'isActive' => true,
        ]);
        return Response::success($attendee->toArray(), 'Attendee created successfully');
    }

    public function updateAttendee(string $attendeeId): array
    {
        $attendee = Attendee::find($attendeeId, 'id')->update(
            ['firstName' => 'Chick', 'lastName' => 'Peas']
        );
        return Response::success(['updated' => $attendee], 'Attendee updated successfully');
    }

    public function deleteAttendee(string $attendeeId): array
    {
        $attendee = Attendee::find($attendeeId)->delete();
        return Response::success(['deleted' => $attendee], 'Attendee deleted successfully');
    }

    public function getAttendeeEvents(string $attendeeId): array
    {
        $attendee = Attendee::with('events')->find($attendeeId);
        $events = $attendee->events;
        return Response::success($events->toArray(), 'Attendee events retrieved successfully');
    }

    public function registerForEvent(string | int $attendeeId, string | int $eventId): array
    {
        try {
            $attendee = Attendee::find($attendeeId);
            if (!$attendee) {
                return Response::error('Attendee not found', 404);
            }

            $event = Event::find($eventId);
            if (!$event) {
                return Response::error('Event not found', 404);
            }

            $exists = $attendee->activeEvents()->where('eventId', $eventId)->exists();
            if ($exists) {
                return Response::error('Attendee already registered for event', 400);
            }

            $existsWithUnregistered = $attendee->allEventHistory()
                ->where('eventId', $eventId)
                ->whereNotNull('unregisteredAt')
                ->exists();

            if ($existsWithUnregistered) {
                $attendee
                    ->allEventHistory()
                    ->updateExistingPivot(
                        $eventId,
                        [
                            'registeredAt' => Carbon::now(),
                            'unregisteredAt' => null,
                        ]
                    );
            } else {
                $attendee
                    ->events()
                    ->attach(
                        $eventId,
                        [
                            'registeredAt' => Carbon::now(),
                        ]
                    );
            }

            return Response::success([
                'attendee_id' => $attendeeId,
                'event_id' => $eventId
            ], 'Successfully registered for event');
        } catch (Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function unregisterForEvent(string | int $attendeeId, string | int $eventId): array
    {
        try {
            $attendee = Attendee::find($attendeeId);
            if (!$attendee) {
                return Response::error('Attendee not found', 404);
            }

            $event = Event::find($eventId);
            if (!$event) {
                return Response::error('Event not found', 404);
            }

            $exists = $attendee
                ->activeEvents()
                ->where(
                    'eventId',
                    $eventId
                )->exists();

            if (!$exists) {
                return Response::error('Attendee is not registered for event', 400);
            }

            $attendee->activeEvents()
                ->updateExistingPivot(
                    $eventId,
                    [
                        'unregisteredAt' => Carbon::now()->toDateTime(),
                    ]
                );

            return Response::success([
                'attendee_id' => $attendeeId,
                'event_id' => $eventId
            ], 'Successfully unregistered from event');
        } catch (Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
