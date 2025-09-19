<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;


/** TODO: Function to return venues that promoter promotes */

class Promoter extends Model
{
    protected $table = 'promoter';

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

    protected function cast()
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
            'event_promoters',
            'userId',
            'eventId'
        );
    }
}
