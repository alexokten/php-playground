<?php

declare(strict_types=1);

class EventRepository
{
    public function findAll()
    {
        return Event::orderBy('createdAt', 'desc')->get();
    }
}
