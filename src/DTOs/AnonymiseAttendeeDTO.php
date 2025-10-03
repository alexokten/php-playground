<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Support\Carbon;
use Router\RequestItem;

class AnonymiseAttendeeDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $email,
        public readonly ?Carbon $anonymisedAt,
        public readonly ?string $firstName = 'Deleted',
        public readonly ?string $lastName = 'User',
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $city = 'Unknown',
        public readonly ?bool $isActive = false,
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
            anonymisedAt: $data['anonymisedAt'] ?? null,
        );
    }

    public static function fromAttendee(int $attendeeId): self
    {
        return new self(
            id: $attendeeId,
            email: "deleted.{$attendeeId}@anonymised.local",
            anonymisedAt: Carbon::now(),
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'dateOfBirth' => $this->dateOfBirth,
            'city' => $this->city,
            'isActive' => $this->isActive,
            'anonymisedAt' => $this->anonymisedAt,
        ];
    }
}
