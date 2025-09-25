<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EventRepository
{
    public function findAll(): Collection
    {
        return Event::all();
    }

    public function findById(int $id): Collection
    {
        return Event::find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data): bool
    {
        return $event->update($data);
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }

    public function findEventsByPromoterId(int $promoterId): Collection
    {
        return Event::where('promoterId', $promoterId)->get();
    }

    public function findAllAttendeesForEvent(Event $event): Collection
    {
        return $event->attendees()->get();
    }

    public function findAllFutureEvents()
    {
        return Event::where('eventDate', ">", Carbon::now())
            ->get();
    }

    public function findAllPastEvents()
    {
        return Event::where('eventDate', "<", Carbon::now())
            ->get();
    }

    public function findAllFutureEventsWithTickets(): Collection
    {
        return Event::whereIsInFuture()
            ->withCount(['attendees' => function ($query) {
                $query->wherePivotNull('unregisteredAt');
            }])
            ->having('attendees_count', '<', DB::raw('maxTickets'))
            ->get();
    }

    public function findAllFutureEventsThatAreSoldOut(): Collection
    {
        return new Event()->whereIsInFuture()
            ->withCount(['attendees' => function ($query) {
                $query->wherePivotNull('unregisteredAt');
            }])
            ->having('attendees_count', '===', DB::raw('maxTickets'))
            ->get();
    }

    public function checkEventHasTickets(Event $event): bool
    {
        $registeredCount = $event
            ->attendees()
            ->wherePivotNull('unregisteredAt')
            ->count();
        return $registeredCount < $event->maxTickets;
    }
}
