<?php

declare(strict_types=1);

use App\Helpers\Response;
use Brick\JsonMapper\JsonMapperException;

class ExceptionHandler
{
    public static function handle(Throwable $e): void
    {
        switch (true) {
            case $e instanceof JsonMapperException:
                Response::sendError($e->getMessage(), 400);
            case $e instanceof InvalidArgumentException:
                Response::sendError($e->getMessage(), 404);
                break;
            case $e instanceof RuntimeException:
                Response::sendError($e->getMessage(), 409);
                break;
            case $e instanceof \Exception:
                Response::sendError($e->getMessage(), 500);
                break;
            default:
                Response::sendError($e->getMessage(), 500);
                break;
        }
    }
}
