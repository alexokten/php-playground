<?php

declare(strict_types=1);

namespace App\DTOs;

use InvalidArgumentException;

class GetAllAttendeesDTO
{
    public function __construct(
        public readonly int $attendeeId
    ) {
        if ($attendeeId < 0) {
            throw new InvalidArgumentException('attendeeId must be greater than 0');
        }
    }
}
