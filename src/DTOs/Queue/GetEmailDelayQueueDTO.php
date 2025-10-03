<?php

declare(strict_types=1);

namespace App\DTOs\Queue;

class GetEmailDelayQueueDTO
{
    public function __construct(
        public readonly int $delay,
    ) {}

    public function getDelayInt(): int
    {
        return $this->delay;
    }
}
