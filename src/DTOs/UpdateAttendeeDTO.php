<?php

declare(strict_types=1);

namespace App\DTOs;

use Router\RequestItem;

class UpdateAttendeeDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $email = null,
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $city = null,
        public readonly ?bool $isActive = null
    ) {}

    public static function create(RequestItem $request): self
    {
        $data = json_decode($request->body, true);
        return new self(
            id: (int)$data['id'],
            firstName: $data['firstName'] ?? null,
            lastName: $data['lastName'] ?? null,
            email: $data['email'] ?? null,
            dateOfBirth: $data['dateOfBirth'] ?? null,
            city: $data['city'] ?? null,
            isActive: $data['isActive'] ?? null,
        );
    }


    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'dateOfBirth' => $this->dateOfBirth,
            'city' => $this->city,
            'isActive' => $this->isActive,
        ], fn($value) => $value !== null);
    }
}
