<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $table = 'attendees';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

    #[\Override]
    protected function casts()
    {
        return [
            'firstName' => 'string',
            'lastName' => 'string',
            'email' => 'string',
            'dateOfBirth' => 'datetime',
            'city' => 'string',
            'isActive' => 'bool',
            'anonymisedAt' => 'datetime',
        ];
    }


    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'events_attendees',
            'attendeeId',
            'eventId'
        )->withPivot(['registeredAt', 'unregisteredAt', 'attendedAt']);
    }

    public function activeEvents()
    {
        return $this->belongsToMany(
            Event::class,
            'events_attendees',
            'attendeeId',
            'eventId'
        )->wherePivotNull('unregisteredAt')
            ->withPivot(['registeredAt', 'unregisteredAt', 'attendedAt']);
    }

    public function allEventHistory()
    {
        return $this->belongsToMany(
            Event::class,
            'events_attendees',
            'attendeeId',
            'eventId'
        )->withPivot(
            ['registeredAt', 'unregisteredAt', 'attendedAt']
        );
    }
}
