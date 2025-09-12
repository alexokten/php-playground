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

    protected $casts = [
        'eventDate' => 'datetime',
        'isActive' => 'boolean',
        'maxTickets' => 'integer',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime'
    ];

    protected $attributes = [
        'isActive' => true,
        'maxTickets' => 50
    ];

    public function isFutureEvent(): bool
    {
        return $this->eventDate && $this->eventDate->isFuture();
    }

    public function isPastEvent(): bool
    {
        return $this->eventDate && $this->eventDate->isPast();
    }
}
