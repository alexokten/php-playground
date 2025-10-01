<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = ['id', 'createdAt', 'updatedAt'];

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

    public function attendees()
    {
        return $this->belongsToMany(
            Attendee::class,
            'events_attendees',
            'eventId',
            'attendeeId'
        )->withPivot(['registeredAt', 'unregisteredAt', 'attendedAt']);
    }

    public function promoter()
    {
        return $this->belongsTo(Promoter::class, 'promoterId');
    }

    public function scopeWhereIsInFuture(Builder $query): Builder
    {
        return $query->where('eventDate', '>', Carbon::now());
    }

    public function scopeWhereIsInPast(Builder $query): Builder
    {
        return $query->where('eventDate', '<', Carbon::now());
    }

    public function scopeWhereIsSoldOut(Builder $query): Builder
    {
        return $query->withCount(
            ['attendees' => function ($q) {
                $q->whereNull('unregisteredAt');
            }]
        )->havingRaw('attendees_count >= maxTickets');
    }
}
