<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attendee;
use Illuminate\Database\Eloquent\Collection;

class AttendeeRepository
{
    public function findAll(): Collection
    {
        return Attendee::all();
    }

    public function findById(int $id): ?Attendee
    {
        return Attendee::find($id);
    }

    public function findByEmail(string $email): ?Attendee
    {
        return Attendee::where('email', $email)->first();
    }

    public function emailExists(string $email): bool
    {
        return Attendee::where('email', $email)->exists();
    }

    public function create(array $data): Attendee
    {
        return Attendee::create($data);
    }

    public function update(Attendee $attendee, array $data): bool
    {
        return $attendee->update($data);
    }

    public function delete(Attendee $attendee): ?bool
    {
        return $attendee->delete();
    }

    public function findWithEvents(int $id): ?Attendee
    {
        return Attendee::with('events')->find($id);
    }

    public function findWithActiveEvents(int $id): ?Attendee
    {
        return Attendee::with('activeEvents')->find($id);
    }

    public function isRegisteredForEvent(int $attendeeId, int $eventId): bool
    {
        return Attendee::find($attendeeId)
            ?->activeEvents()
            ->where('eventId', $eventId)
            ->exists() ?? false;
    }

    public function registerForEvent(int $attendeeId, int $eventId, array $pivotData): void
    {
        $attendee = Attendee::find($attendeeId);
        $attendee?->events()->sync([
            $eventId => $pivotData
        ], false);
    }

    public function unregisterFromEvent(int $attendeeId, int $eventId, array $pivotData): void
    {
        $attendee = Attendee::find($attendeeId);
        $attendee?->events()->sync([
            $eventId => $pivotData
        ], false);
    }
}
