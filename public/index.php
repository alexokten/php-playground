<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../router.php';
require_once __DIR__ . '/../database/connection.php';

use App\Controllers\AttendeeController;

ray()->clearAll();

try {

    $router = new Router();
    $router
        ->get('/api/attendee/all', [AttendeeController::class, 'getAllAttendees'])
        ->get('/api/attendee/get/:id', [AttendeeController::class, 'getAttendeeById'])
        ->get('/api/attendee/events', [AttendeeController::class, 'getAttendeeEvents'])
        ->post('/api/attendee/create', [AttendeeController::class, 'createAttendee'])
        ->put('/api/attendee/update', [AttendeeController::class, 'updateAttendee'])
        ->put('/api/attendee/anonymise', [AttendeeController::class, 'anonymiseAttendee'])
        ->post('/api/event/register', [AttendeeController::class, 'registerForEvent'])
        ->post('/api/event/unregister', [AttendeeController::class, 'unRegisterForEvent']);

    $router->dispatch();
} catch (Exception $e) {
    if ($_ENV['APP_ENV'] === 'development') {
        echo $e;
        ray($e);
    }
}
