<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

/** Remaining tasks:
  1. getAttendeeEvents() - Show which events a Attendee is registered for
  2. registerForEvent() - Register Attendee for an event
  3. unregisterFromEvent() - Cancel event registration
  4. Validation logic - Make sure data is valid before saving
  5. Error handling - Handle cases like "Attendee not found"

  I'd suggest starting with getAttendeeEvents() since it's read-only and will help
   you understand the relationships.

  getAttendeeEvents() should:
  - Take a Attendee ID
  - Return all events that Attendee is registered for
  - Use your pivot table (event_attendees) to find the relationships

  Want to tackle getAttendeeEvents() next? This will involve:
  - Finding the Attendee
  - Getting their events through the pivot table
  - Returning the event details */


class AttendeeController
{
    public function index(): JsonResponse
    {
        $Attendees = Attendee::all();
        return new JsonResponse($Attendees);
    }

    public function show(string $id): JsonResponse
    {
        $AttendeeWithId = Attendee::where('id', $id)->get();
        return new JsonResponse($AttendeeWithId);
    }

    public function store(): JsonResponse
    {
        $uuid = bin2hex(random_bytes((16)));
        $Attendee = Attendee::create([
            'firstName' => 'Jeff',
            'lastName' => 'Hat',
            'email' => 'example' . $uuid . 'example.com',
            'dateOfBirth' => Carbon::createFromFormat('d/m/Y', '19/10/2020'),
            'city' => 'Bristol',
            'isActive' => true,
        ]);
        return new JsonResponse($Attendee);
    }

    public function update(string $id): JsonResponse
    {
        $Attendee = Attendee::find($id, 'id')->update(
            ['firstName' => 'Chick', 'lastName' => 'Peas']
        );
        return new JsonResponse($Attendee);
    }

    public function delete(string $id): JsonResponse
    {
        $Attendee = Attendee::find($id)->delete();
        return new JsonResponse($Attendee);
    }

    public function getsAttendeeEvents(string $id): JsonResponse
    {
        $attendee = Attendee::with('events')->find($id);
        $events = $attendee->events;
        return new JsonResponse($events);
    }
}
