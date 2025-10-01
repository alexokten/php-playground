<?php

declare(strict_types=1);

namespace App\DTOs\Event;

class GetSoldTicketsPercentageDTO
{
    public function __construct(
        public int $id,
    ) {}
}
