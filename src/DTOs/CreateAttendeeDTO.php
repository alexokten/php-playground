<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Support\Carbon;

class CreateAttendeeDTO
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $dateOfBirth,
        public readonly string $city,
        public readonly bool $isActive = true
    ) {}

    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'dateOfBirth' => $this->dateOfBirth,
            'city' => $this->city,
            'isActive' => $this->isActive,
        ];
    }

    public function toDatabaseArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'dateOfBirth' => Carbon::createFromFormat('Y-m-d', $this->dateOfBirth),
            'city' => $this->city,
            'isActive' => $this->isActive,
        ];
    }
}
