<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class EventAttendees extends Model
{
    protected $table = 'event_attendees';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'userId' => 'int',
        'eventId' => 'int',
        'attendedAt' => 'datetime',
        'registeredAt' => 'datetime',
    ];

    public function attendee()
    {
        return $this->belongsTo(Attendee::class, 'userId');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'eventId');
    }
}
