<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendees extends Model
{
    protected $table = 'events_attendees';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'attendeeId' => 'int',
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
