<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promoter extends Model
{
    protected $table = 'promoter';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

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
