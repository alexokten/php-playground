<?php

declare(strict_types=1);

namespace App\DTOs\Event;

class GetEventDTO
{
    public function __construct(
        public int $id,
    ) {}
}