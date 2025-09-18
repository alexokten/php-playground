<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateEventAttendeesColumnNames extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('events_attendees');

        $table->renameColumn('attendee_id', 'attendeeId')
            ->renameColumn('event_id', 'eventId')
            ->update();
    }
}
