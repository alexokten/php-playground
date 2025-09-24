<?php

declare(strict_types=1);

namespace App\DTOs;

use InvalidArgumentException;

class GetAttendeeDTO
{
    public function __construct(
        public readonly int | string $attendeeId
    ) {
        if ($attendeeId < 0) {
            throw new InvalidArgumentException(
                'attendeeId must be greater than 0'
            );
        }
    }
}
