<?php

declare(strict_types=1);

//TODO: Write query/use scoped functions in model to make it easier/effecient

class EventService
{
    public static function GetAllEventsInTheFutureThatHaveTicketsAvailable()
    {
        return Event::where('createdAt', 'desc')->get();
    }
}
