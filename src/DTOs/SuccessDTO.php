<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Support\Carbon;

class SuccessDTO
{
    public function __construct(
        public readonly string $timestamp,
        public readonly bool $success = true,
        public readonly string $message = 'Success',
        public readonly array $data = [],
        public readonly int $statusCode = 200,
    ) {}

    public static function create(array $data, string $message): self
    {
        return new self(
            success: true,
            message: $message,
            data: $data,
            statusCode: 200,
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
