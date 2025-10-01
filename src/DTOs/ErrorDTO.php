<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Support\Carbon;

class ErrorDTO
{
    public function __construct(
        public readonly string $timestamp,
        public readonly bool $success = false,
        public readonly string $message = 'Error',
        public readonly array $data = [],
        public readonly int $statusCode = 500,
    ) {}

    public static function create(array $data, string $message, int $statusCode): self
    {
        return new self(
            success: false,
            message: $message,
            data: $data,
            statusCode: $statusCode ?? 500,
            timestamp: Carbon::now()->toISOString(),
        );
    }

    public function toArray()
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'statusCode' => $this->statusCode,
            'timestamp' => $this->timestamp,
        ];
    }
}
