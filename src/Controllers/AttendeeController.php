<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateAttendeeDTO;
use App\DTOs\GetAttendeeDTO;
use App\DTOs\RegisterForEventDTO;
use App\DTOs\UpdateAttendeeDTO;
use App\Helpers\ExceptionHandler;
use App\Helpers\Response;
use App\Repositories\AttendeeRepository;
use App\Services\AttendeeService;
use Brick\JsonMapper\JsonMapper;
use RequestItem;
use Throwable;

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

class AttendeeController
{
    private readonly AttendeeService $attendeeService;
    private readonly JsonMapper $jsonMapper;

    public function __construct()
    {
        $this->attendeeService = new AttendeeService(new AttendeeRepository());
        $this->jsonMapper = new JsonMapper();
    }

    public function getAllAttendees(): void
    {
        try {
            $attendees = $this->attendeeService->getAllAttendees();
            Response::sendSuccess(
                [$attendees->toArray()],
                'Attendees retrieved successfully'
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getAttendeeById(RequestItem $request): void
    {
        try {
            $dto = new GetAttendeeDTO((int)$request->params[':id']);
            $attendee = $this->attendeeService->getAttendeeById($dto->id);
            Response::sendSuccess(
                [$attendee->toArray()],
                'Attendee retrieved successfully'
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function createAttendee(RequestItem $request): void
    {
        ray('hey');
        try {
            $dto = $this->jsonMapper->map($request->body, CreateAttendeeDTO::class);
            $result = $this->attendeeService->createAttendee($dto);
            Response::sendSuccess(
                $result,
                'Attendee created successfully'
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function updateAttendee(RequestItem $request): void
    {
        try {
            $dto = $this->jsonMapper->map($request->body, UpdateAttendeeDTO::class);
            $result = $this->attendeeService->updateAttendee($dto);
            Response::sendSuccess(
                $result,
                'Attendee updated successfully'
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function anonymiseAttendee(RequestItem $request): void
    {
        try {
            $data = json_decode($request->body, true);
            $attendeeId = (int)$data['id'];
            $result = $this->attendeeService->anonymizeAttendee($attendeeId);
            Response::sendSuccess(
                $result,
                'Attendee anonymised successfully'
            );
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getAttendeeEvents(RequestItem $request): void
    {
        try {
            $attendeeId = (int)$request->params[':id'];
            $events = $this->attendeeService->getAttendeeEvents($attendeeId);
            Response::sendSuccess($events, 'Attendee events retrieved successfully');
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function registerForEvent(RequestItem $request): void
    {
        try {
            $dto = $this->jsonMapper->map($request->body, RegisterForEventDTO::class);
            $result = $this->attendeeService->registerForEvent($dto);
            Response::sendSuccess($result, 'Successfully registered for event');
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function unregisterForEvent(RequestItem $request): void
    {
        try {
            $dto = $this->jsonMapper->map($request->body, RegisterForEventDTO::class);
            $result = $this->attendeeService->unregisterFromEvent($dto);
            Response::sendSuccess($result, 'Successfully unregistered from event');
        } catch (Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }
}
