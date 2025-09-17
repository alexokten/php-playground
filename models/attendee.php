<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $table = 'users';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'firstName',
        'lastName',
        'dateOfBirth',
        'city',
        'isActive',
    ];

    protected $casts = [
        'firstName' => 'string',
        'lastName' => 'string',
        'dateOfBirth' => 'datetime',
        'city' => 'string',
        'isActive' => 'bool',
    ];

    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'event_attendees',
            'userId',
            'eventId'
        );
    }
}
