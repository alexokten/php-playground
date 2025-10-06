<?php

declare(strict_types=1);

use App\Controllers\AttendeeController;
use App\Controllers\EventController;
use App\Controllers\ImageBenchmarkController;
use App\Controllers\QueueController;
use Router\Router;

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../router.php';
    require_once __DIR__ . '/../database/connection.php';


    ray()->clearAll();

    $router = new Router();
    $router
        ->get('/api/attendee/all', [AttendeeController::class, 'getAllAttendees'])
        ->get('/api/attendee/get/:id', [AttendeeController::class, 'getAttendeeById'])
        ->get('/api/attendee/events/:id', [AttendeeController::class, 'getAttendeeEvents'])
        ->post('/api/attendee/create', [AttendeeController::class, 'createAttendee'])
        ->put('/api/attendee/update', [AttendeeController::class, 'updateAttendee'])
        ->put('/api/attendee/anonymise', [AttendeeController::class, 'anonymiseAttendee'])
        ->post('/api/event/register', [AttendeeController::class, 'registerForEvent'])
        ->post('/api/event/unregister', [AttendeeController::class, 'unRegisterForEvent'])

        ->get('/api/event/all', [EventController::class, 'getAllEvents'])
        ->get('/api/event/sales/percentage/:id', [EventController::class, 'getEventTicketSalesAsPercentage'])

        ->post('/api/queue/test-email/:delay', [QueueController::class, 'sendTestEmail'])

        ->get('/api/image-benchmark', [ImageBenchmarkController::class, 'runBenchmark'])
        ->get('/api/images/list', [ImageBenchmarkController::class, 'listAvailableImages']);


    $router->dispatch();
} catch (Exception $e) {
    if ($_ENV['APP_ENV'] === 'development') {
        echo $e;
        ray($e);
        //sentry()
    }
}
