<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../router.php';
require_once __DIR__ . '/../database/connection.php';

ray()->clearAll();

use App\Controllers\AttendeeController;
use App\Helpers\Response;

try {

    $router = new Router();
    $router
        ->get('/api/attendees', [AttendeeController::class, 'getAllAttendees'])
        ->get('/api/attendee/:id', [AttendeeController::class, 'getAttendeeById']);

    // $router->post('/api/attendee/create', [AttendeeController::class, 'createAttendee']);
    // $router->put('/api/attendee/update/:attendeeId', [AttendeeController::class, 'updateAttendee']);
    // $router->delete('/api/attendee/delete/:attendeeId', [AttendeeController::class, 'deleteAttendee']);

    // $router->get('/api/events/:attendeeId', [AttendeeController::class, 'getAttendeeEvents']);
    // $router->get('/api/events/register/:eventId/:attendeeId', [AttendeeController::class, 'registerForEvent']);
    // $router->get('/api/events/unregister/:eventId/:attendeeId', [AttendeeController::class, 'unregisterForEvent']);

    $router->dispatch();
} catch (Exception $e) {
    if ($_ENV['APP_ENV'] === 'development') {
        ray($e);
    }
}
