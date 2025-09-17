<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../router.php';
require_once __DIR__ . '/../controllers/attendee.php';
require_once __DIR__ . '/../models/attendee.php';
require_once __DIR__ . '/../models/event.php';
require_once __DIR__ . '/../database/connection.php';

ray()->clearAll();

$router = new Router();

$router->get('/api/attendees/all', function ($req, $res) {
    $AttendeeController = new AttendeeController();
    $response = $AttendeeController->index();
    ray()->toJson($response);
    $res::sendResponse();
});

$router->get('/api/attendee/id/:attendeeId', function ($req, $res) {
    $AttendeeController = new AttendeeController();
    $response = $AttendeeController->show($req->params[':attendeeId']);
    ray()->toJson($response);
    $res::sendResponse();
});

$router->get('/api/attendee/create', function ($req, $res) {
    $AttendeeController = new AttendeeController();
    $response = $AttendeeController->store();
    ray()->toJson($response);
    $res::sendResponse();
});

$router->get('/api/attendee/update/:attendeeId', function ($req, $res) {
    $AttendeeController = new AttendeeController();
    $response = $AttendeeController->update($req->params[':attendeeId']);
    ray()->toJson($response);
    $res::sendResponse();
});

$router->get('/api/attendee/delete/:attendeeId', function ($req, $res) {
    $AttendeeController = new AttendeeController();
    $response = $AttendeeController->delete($req->params[':attendeeId']);
    ray()->toJson($response);
    $res::sendResponse();
});

$router->get('/api/events/:attendeeId', function ($req, $res) {
    $AttendeeController = new AttendeeController();
    $response = $AttendeeController->getsAttendeeEvents($req->params[':attendeeId']);
    ray()->toJson($response);
    $res::sendResponse();
});

$router->dispatch();
