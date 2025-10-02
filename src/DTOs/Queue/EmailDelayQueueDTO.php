<?php

declare(strict_types=1);

namespace App\DTOs;

use RequestItem;

class GetEmailDelayQueueDTO
{
    public function __construct(
        public readonly ?int $delay,
    ) {}
}
