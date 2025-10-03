<?php

declare(strict_types=1);

namespace App\DTOs;

use Router\RequestItem;

class GetAttendeeDTO
{
    public function __construct(
        public readonly ?int $id,
    ) {}

    public static function getId(RequestItem $request): int
    {
        $data = json_decode($request->body, true);
        return $data['id'];
    }
}
