<?php

declare(strict_types=1);

namespace App\Helpers;

use App\DTOs\SuccessDTO;
use App\DTOs\ErrorDTO;

class Response
{
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    public static function success(array $data, string $message = "Successful response message"): array
    {
        return SuccessDTO::create($data, $message)->toArray();
    }

    public static function error(string $message = "An error occurred", int $statusCode = self::HTTP_INTERNAL_SERVER_ERROR, array $data = []): array
    {
        return ErrorDTO::create($data, $message, $statusCode)->toArray();
    }

    public static function sendSuccess(array $data, string $message = "Success"): void
    {
        $response = self::success($data, $message);
        self::sendJson($response);
    }

    public static function sendError(string $message, int $statusCode = self::HTTP_INTERNAL_SERVER_ERROR, array $data = []): void
    {
        $response = self::error($message, $statusCode, $data);
        self::sendJson($response);
    }

    private static function sendJson(array $data): void
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        http_response_code($data['status_code'] ?? 200);

        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }
}
