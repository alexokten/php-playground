<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/** Remaining tasks:
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

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

class AttendeeController
{
    public function index(): JsonResponse
    {
        $attendees = Attendee::all();
        return new JsonResponse($attendees);
    }

    public function show(string $id): JsonResponse
    {
        $attendeeWithId = Attendee::where('id', $id)->get();
        return new JsonResponse($attendeeWithId);
    }

    public function store(): JsonResponse
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
        return new JsonResponse($attendee);
    }

    public function update(string $id): JsonResponse
    {
        $attendee = Attendee::find($id, 'id')->update(
            ['firstName' => 'Chick', 'lastName' => 'Peas']
        );
        return new JsonResponse($attendee);
    }

    public function delete(string $id): JsonResponse
    {
        $attendee = Attendee::find($id)->delete();
        return new JsonResponse($attendee);
    }

    public function getsAttendeeEvents(string $eventId): JsonResponse
    {
        $attendee = Attendee::with('events')->find($eventId);
        $events = $attendee->events;
        return new JsonResponse($events);
    }

    public function registerForEvent(string | int $attendeeId, string | int $eventId): string

    {
        try {
            ray('hello');
            $attendee = Attendee::find($attendeeId);
            if (!$attendee) {
                return json_encode(['error', 'Attendee not found'], 500);
            }

            $event = Event::find($eventId);
            if (!$event) {
                return json_encode(['error', 'Event not found'], 500);
            }

            $exists = $attendee->events()->where('eventId', $eventId)->exists();
            if ($exists) {
                return json_encode(['error', 'Attendee already registered for event'], 500);
            }

            $attendee->events()->attach($eventId, [
                'registeredAt' => Carbon::now(),
            ]);

            return json_encode([
                'message' => 'Successfully registered for event',
                'attendee_id' => $attendeeId,
                'event_id' => $eventId
            ]);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()], 500);
        }
    }

    public function unregisterForEvent(string | int $attendeeId, string | int $eventId): string

    {
        try {
            $attendee = Attendee::find($attendeeId);
            if (!$attendee) {
                return json_encode(['error', 'Attendee not found'], 500);
            }

            $event = Event::find($eventId);
            if (!$event) {
                return json_encode(['error', 'Event not found'], 500);
            }

            $exists = $attendee->events()->where('eventId', $eventId)->exists();
            if (!$exists) {
                return json_encode(['error', 'Attendee is not registered for event'], 500);
            }

            $attendee->events()->updateExistingPivot($eventId, [
                'unregisteredAt' => Carbon::now(),
            ]);

            return json_encode([
                'message' => 'Successfully registered for event',
                'attendee_id' => $attendeeId,
                'event_id' => $eventId
            ]);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()], 500);
        }
    }
}

// TODO:

// <?php

// declare(strict_types=1);

// use Carbon\Carbon;

// class AttendeeController
// {
//     private const HTTP_OK = 200;
//     private const HTTP_BAD_REQUEST = 400;
//     private const HTTP_NOT_FOUND = 404;
//     private const HTTP_CONFLICT = 409;
//     private const HTTP_INTERNAL_ERROR = 500;

//     /**
//      * Registers an attendee for a specific event with validation and error handling
//      */
//     public function registerForEvent(string|int $attendeeId, string|int $eventId): array
//     {
//         try {
//             $validationResult = $this->validateEventRegistration($attendeeId, $eventId);
//             if (!$validationResult['success']) {
//                 return $this->createErrorResponse(
//                     $validationResult['message'], 
//                     $validationResult['status_code']
//                 );
//             }

//             $attendee = $validationResult['attendee'];
//             $event = $validationResult['event'];

//             // Check if attendee is currently registered (not unregistered)
//             $isCurrentlyRegistered = $this->isAttendeeRegisteredForEvent($attendee, $eventId);
//             if ($isCurrentlyRegistered) {
//                 return $this->createErrorResponse(
//                     'Attendee already registered for event', 
//                     self::HTTP_CONFLICT
//                 );
//             }

//             // Check for previous registration that was unregistered
//             $previousRegistration = $this->getPreviousUnregisteredAttendance($attendee, $eventId);
            
//             if ($previousRegistration) {
//                 $this->reactivateRegistration($attendee, $eventId);
//             } else {
//                 $this->createNewRegistration($attendee, $eventId);
//             }

//             return $this->createSuccessResponse(
//                 'Successfully registered for event',
//                 $attendeeId,
//                 $eventId
//             );

//         } catch (Exception $e) {
//             return $this->createErrorResponse($e->getMessage(), self::HTTP_INTERNAL_ERROR);
//         }
//     }

//     /**
//      * Unregisters an attendee from a specific event using soft deletion
//      */
//     public function unregisterFromEvent(string|int $attendeeId, string|int $eventId): array
//     {
//         try {
//             $validationResult = $this->validateEventRegistration($attendeeId, $eventId);
//             if (!$validationResult['success']) {
//                 return $this->createErrorResponse(
//                     $validationResult['message'], 
//                     $validationResult['status_code']
//                 );
//             }

//             $attendee = $validationResult['attendee'];

//             // Check if attendee is currently registered
//             $isCurrentlyRegistered = $this->isAttendeeRegisteredForEvent($attendee, $eventId);
//             if (!$isCurrentlyRegistered) {
//                 return $this->createErrorResponse(
//                     'Attendee is not currently registered for this event', 
//                     self::HTTP_BAD_REQUEST
//                 );
//             }

//             $this->softDeleteRegistration($attendee, $eventId);

//             return $this->createSuccessResponse(
//                 'Successfully unregistered from event',
//                 $attendeeId,
//                 $eventId
//             );

//         } catch (Exception $e) {
//             return $this->createErrorResponse($e->getMessage(), self::HTTP_INTERNAL_ERROR);
//         }
//     }

//     /**
//      * Validates that both attendee and event exist and returns them
//      */
//     private function validateEventRegistration(string|int $attendeeId, string|int $eventId): array
//     {
//         $attendee = Attendee::find($attendeeId);
//         if (!$attendee) {
//             return [
//                 'success' => false,
//                 'message' => 'Attendee not found',
//                 'status_code' => self::HTTP_NOT_FOUND
//             ];
//         }

//         $event = Event::find($eventId);
//         if (!$event) {
//             return [
//                 'success' => false,
//                 'message' => 'Event not found',
//                 'status_code' => self::HTTP_NOT_FOUND
//             ];
//         }

//         return [
//             'success' => true,
//             'attendee' => $attendee,
//             'event' => $event
//         ];
//     }

//     /**
//      * Checks if attendee is currently registered for event (not unregistered)
//      */
//     private function isAttendeeRegisteredForEvent(Attendee $attendee, string|int $eventId): bool
//     {
//         return $attendee->events()
//             ->wherePivot('eventId', $eventId)
//             ->wherePivotNull('unregisteredAt')
//             ->exists();
//     }

//     /**
//      * Gets previous registration that was unregistered
//      */
//     private function getPreviousUnregisteredAttendance(Attendee $attendee, string|int $eventId): bool
//     {
//         return $attendee->events()
//             ->wherePivot('eventId', $eventId)
//             ->wherePivotNotNull('unregisteredAt')
//             ->exists();
//     }

//     /**
//      * Reactivates a previous registration by clearing unregisteredAt timestamp
//      */
//     private function reactivateRegistration(Attendee $attendee, string|int $eventId): void
//     {
//         $attendee->events()->updateExistingPivot($eventId, [
//             'unregisteredAt' => null,
//             'registeredAt' => Carbon::now(),
//         ]);
//     }

//     /**
//      * Creates a new registration record
//      */
//     private function createNewRegistration(Attendee $attendee, string|int $eventId): void
//     {
//         $attendee->events()->attach($eventId, [
//             'registeredAt' => Carbon::now(),
//             'unregisteredAt' => null,
//         ]);
//     }

//     /**
//      * Soft deletes a registration by setting unregisteredAt timestamp
//      */
//     private function softDeleteRegistration(Attendee $attendee, string|int $eventId): void
//     {
//         $attendee->events()->updateExistingPivot($eventId, [
//             'unregisteredAt' => Carbon::now(),
//         ]);
//     }

//     /**
//      * Creates a standardised success response
//      */
//     private function createSuccessResponse(string $message, string|int $attendeeId, string|int $eventId): array
//     {
//         return [
//             'success' => true,
//             'message' => $message,
//             'data' => [
//                 'attendee_id' => $attendeeId,
//                 'event_id' => $eventId,
//                 'timestamp' => Carbon::now()->toISOString()
//             ],
//             'status_code' => self::HTTP_OK
//         ];
//     }

//     /**
//      * Creates a standardised error response
//      */
//     private function createErrorResponse(string $message, int $statusCode): array
//     {
//         return [
//             'success' => false,
//             'error' => $message,
//             'status_code' => $statusCode,
//             'timestamp' => Carbon::now()->toISOString()
//         ];
//     }
// }
