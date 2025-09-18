<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $table = 'attendees';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'dateOfBirth',
        'city',
        'isActive',
    ];

    protected function casts()
    {
        return [
            'firstName' => 'string',
            'lastName' => 'string',
            'email' => 'string',
            'dateOfBirth' => 'datetime',
            'city' => 'string',
            'isActive' => 'bool',
        ];
    }


    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'events_attendees',
            'attendeeId',
            'eventId'
        );
    }
}
