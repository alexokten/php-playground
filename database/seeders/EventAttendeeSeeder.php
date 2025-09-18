<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EventAttendeeSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            // John (ID 16) - Tech enthusiast, attends multiple events
            ['attendeeId' => 16, 'eventId' => 1, 'registeredAt' => '2024-05-01 10:30:00', 'createdAt' => '2024-05-01 10:30:00', 'updatedAt' => '2024-05-01 10:30:00'],
            ['attendeeId' => 16, 'eventId' => 3, 'registeredAt' => '2024-07-01 14:20:00', 'createdAt' => '2024-07-01 14:20:00', 'updatedAt' => '2024-07-01 14:20:00'],
            ['attendeeId' => 16, 'eventId' => 6, 'registeredAt' => '2024-10-01 09:00:00', 'createdAt' => '2024-10-01 09:00:00', 'updatedAt' => '2024-10-01 09:00:00'],

            // Sarah (ID 17) - Web developer, interested in workshops
            ['attendeeId' => 17, 'eventId' => 1, 'registeredAt' => '2024-05-02 09:15:00', 'createdAt' => '2024-05-02 09:15:00', 'updatedAt' => '2024-05-02 09:15:00'],
            ['attendeeId' => 17, 'eventId' => 2, 'registeredAt' => '2024-06-15 16:45:00', 'createdAt' => '2024-06-15 16:45:00', 'updatedAt' => '2024-06-15 16:45:00'],
            ['attendeeId' => 17, 'eventId' => 9, 'registeredAt' => '2024-12-01 10:00:00', 'createdAt' => '2024-12-01 10:00:00', 'updatedAt' => '2024-12-01 10:00:00'],

            // Michael (ID 18) - Startup founder
            ['attendeeId' => 18, 'eventId' => 4, 'registeredAt' => '2024-08-01 11:00:00', 'createdAt' => '2024-08-01 11:00:00', 'updatedAt' => '2024-08-01 11:00:00'],
            ['attendeeId' => 18, 'eventId' => 5, 'registeredAt' => '2024-09-01 15:30:00', 'createdAt' => '2024-09-01 15:30:00', 'updatedAt' => '2024-09-01 15:30:00'],
            ['attendeeId' => 18, 'eventId' => 7, 'registeredAt' => '2024-11-05 12:45:00', 'createdAt' => '2024-11-05 12:45:00', 'updatedAt' => '2024-11-05 12:45:00'],

            // Emma (ID 19) - Database specialist
            ['attendeeId' => 19, 'eventId' => 1, 'registeredAt' => '2024-05-05 13:30:00', 'createdAt' => '2024-05-05 13:30:00', 'updatedAt' => '2024-05-05 13:30:00'],
            ['attendeeId' => 19, 'eventId' => 3, 'registeredAt' => '2024-07-20 11:00:00', 'createdAt' => '2024-07-20 11:00:00', 'updatedAt' => '2024-07-20 11:00:00'],
            ['attendeeId' => 19, 'eventId' => 10, 'registeredAt' => '2025-02-01 13:45:00', 'createdAt' => '2025-02-01 13:45:00', 'updatedAt' => '2025-02-01 13:45:00'],

            // James (ID 20) - New to tech
            ['attendeeId' => 20, 'eventId' => 2, 'registeredAt' => '2024-06-25 16:20:00', 'createdAt' => '2024-06-25 16:20:00', 'updatedAt' => '2024-06-25 16:20:00'],
            ['attendeeId' => 20, 'eventId' => 8, 'registeredAt' => '2024-12-15 10:30:00', 'createdAt' => '2024-12-15 10:30:00', 'updatedAt' => '2024-12-15 10:30:00'],

            // Olivia (ID 21) - PHP developer
            ['attendeeId' => 21, 'eventId' => 6, 'registeredAt' => '2024-10-01 12:00:00', 'createdAt' => '2024-10-01 12:00:00', 'updatedAt' => '2024-10-01 12:00:00'],
            ['attendeeId' => 21, 'eventId' => 1, 'registeredAt' => '2024-05-10 14:00:00', 'createdAt' => '2024-05-10 14:00:00', 'updatedAt' => '2024-05-10 14:00:00'],

            // William (ID 22) - Design enthusiast
            ['attendeeId' => 22, 'eventId' => 2, 'registeredAt' => '2024-06-20 15:30:00', 'createdAt' => '2024-06-20 15:30:00', 'updatedAt' => '2024-06-20 15:30:00'],
            ['attendeeId' => 22, 'eventId' => 7, 'registeredAt' => '2024-11-01 14:00:00', 'createdAt' => '2024-11-01 14:00:00', 'updatedAt' => '2024-11-01 14:00:00'],

            // Ava (ID 23) - Full-stack developer
            ['attendeeId' => 23, 'eventId' => 3, 'registeredAt' => '2024-07-05 09:45:00', 'createdAt' => '2024-07-05 09:45:00', 'updatedAt' => '2024-07-05 09:45:00'],
            ['attendeeId' => 23, 'eventId' => 5, 'registeredAt' => '2024-09-15 11:10:00', 'createdAt' => '2024-09-15 11:10:00', 'updatedAt' => '2024-09-15 11:10:00'],

            // Alexander (ID 24) - AI interested
            ['attendeeId' => 24, 'eventId' => 7, 'registeredAt' => '2024-11-01 14:00:00', 'createdAt' => '2024-11-01 14:00:00', 'updatedAt' => '2024-11-01 14:00:00'],
            ['attendeeId' => 24, 'eventId' => 10, 'registeredAt' => '2025-01-15 16:15:00', 'createdAt' => '2025-01-15 16:15:00', 'updatedAt' => '2025-01-15 16:15:00'],

            // Isabella (ID 25) - Learning to code
            ['attendeeId' => 25, 'eventId' => 4, 'registeredAt' => '2024-08-20 14:30:00', 'createdAt' => '2024-08-20 14:30:00', 'updatedAt' => '2024-08-20 14:30:00'],
            ['attendeeId' => 25, 'eventId' => 8, 'registeredAt' => '2024-12-20 09:00:00', 'createdAt' => '2024-12-20 09:00:00', 'updatedAt' => '2024-12-20 09:00:00'],

            // Additional attendees (IDs 26-30) for more coverage
            ['attendeeId' => 26, 'eventId' => 2, 'registeredAt' => '2024-06-30 10:15:00', 'createdAt' => '2024-06-30 10:15:00', 'updatedAt' => '2024-06-30 10:15:00'],
            ['attendeeId' => 27, 'eventId' => 4, 'registeredAt' => '2024-08-15 14:20:00', 'createdAt' => '2024-08-15 14:20:00', 'updatedAt' => '2024-08-15 14:20:00'],
            ['attendeeId' => 28, 'eventId' => 6, 'registeredAt' => '2024-10-20 16:30:00', 'createdAt' => '2024-10-20 16:30:00', 'updatedAt' => '2024-10-20 16:30:00'],
            ['attendeeId' => 29, 'eventId' => 8, 'registeredAt' => '2024-12-25 11:45:00', 'createdAt' => '2024-12-25 11:45:00', 'updatedAt' => '2024-12-25 11:45:00'],
            ['attendeeId' => 30, 'eventId' => 9, 'registeredAt' => '2025-01-05 13:00:00', 'createdAt' => '2025-01-05 13:00:00', 'updatedAt' => '2025-01-05 13:00:00'],
        ];

        $eventAttendees = $this->table('events_attendees');
        $eventAttendees->insert($data)->saveData();
    }
}
