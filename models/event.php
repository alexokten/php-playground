<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'eventDate',
        'location',
        'maxTickets',
        'isActive'
    ];

    protected function cast()
    {
        return [
            'eventDate' => 'datetime',
            'isActive' => 'boolean',
            'maxTickets' => 'integer',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime'
        ];
    }

    protected $attributes = [
        'isActive' => true,
        'maxTickets' => 50
    ];
}
