<?php

declare(strict_types=1);

namespace App\DTOs;

use RequestItem;

class GetAttendeeDTO
{
    public function __construct(
        public readonly ?int $id,
    ) {}

    public static function getId(RequestItem $request)
    {
        $data = json_decode($request->body, true);
        return $data['id'];
    }
}
