<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../router.php';
require_once __DIR__ . '/../controllers/attendee.php';
require_once __DIR__ . '/../models/attendee.php';
require_once __DIR__ . '/../models/event.php';
require_once __DIR__ . '/../database/connection.php';

ray()->clearAll();

use Illuminate\Http\JsonResponse;

try {

    $router = new Router();
    $router->get('/api/attendees/all', function ($req, $res) {
        $attendee = new AttendeeController();
        $response = $attendee->index();
        ray()->toJson($response);
        $res::sendResponse();
    });

    $router->get('/api/attendee/id/:attendeeId', function ($req, $res) {
        $attendee = new AttendeeController();
        $response = $attendee->show($req->params[':attendeeId']);
        ray()->toJson($response);
        $res::sendResponse();
    });

    $router->get('/api/attendee/create', function ($req, $res) {
        $attendee = new AttendeeController();
        $response = $attendee->store();
        ray()->toJson($response);
        $res::sendResponse();
    });

    $router->get('/api/attendee/update/:attendeeId', function ($req, $res) {
        $attendee = new AttendeeController();
        $response = $attendee->update($req->params[':attendeeId']);
        ray()->toJson($response);
        $res::sendResponse();
    });

    $router->get('/api/attendee/delete/:attendeeId', function ($req, $res) {
        $attendee = new AttendeeController();
        $response = $attendee->delete($req->params[':attendeeId']);
        ray()->toJson($response);
        $res::sendResponse();
    });

    $router->get('/api/events/:attendeeId', function ($req, $res) {
        $attendee = new AttendeeController();
        $response = $attendee->getsAttendeeEvents($req->params[':attendeeId']);
        ray()->toJson($response);
        $res::sendResponse();
    });


    $router->get('/api/events/register/:eventId/:attendeeId', function ($req, $res) {
        [':eventId' => $eventId, ':attendeeId' => $attendeeId] = $req->params;
        $response = new AttendeeController()->registerForEvent($attendeeId, $eventId);
        $res::sendResponse(responseJson: $response);
    });

    $router->dispatch();
} catch (Exception $e) {
    if ($_ENV['APP_ENV'] === 'development') {
        ray($e);
    }
    /** else log to sentry */
}
