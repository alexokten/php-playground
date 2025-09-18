<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EventSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'title' => 'Bristol Tech Summit 2024',
                'description' => 'Annual technology conference bringing together Bristol\'s finest developers, designers, and entrepreneurs.',
                'eventDate' => '2024-06-15 09:00:00',
                'location' => 'Engine Shed, Temple Meads',
                'maxTickets' => 300,
                'promoterId' => 9,
                'venueId' => 3,
                'isActive' => 1
            ],
            [
                'id' => 2,
                'title' => 'Harbourside Web Workshop',
                'description' => 'Hands-on workshop covering modern web development techniques with local Bristol experts.',
                'eventDate' => '2024-07-20 10:00:00',
                'location' => 'Watershed Media Centre',
                'maxTickets' => 80,
                'promoterId' => 12,
                'venueId' => 1,
                'isActive' => 1
            ],
            [
                'id' => 3,
                'title' => 'Bristol Database Meetup',
                'description' => 'Monthly meetup for database professionals in Bristol. Pizza and networking included!',
                'eventDate' => '2024-08-10 18:30:00',
                'location' => 'Spike Island Arts Centre',
                'maxTickets' => 50,
                'promoterId' => 14,
                'venueId' => 9,
                'isActive' => 1
            ],
            [
                'id' => 4,
                'title' => 'Clifton Code Camp',
                'description' => 'Intensive weekend coding bootcamp in beautiful Clifton.',
                'eventDate' => '2024-09-14 09:00:00',
                'location' => 'University of Bristol',
                'maxTickets' => 40,
                'promoterId' => 13,
                'venueId' => 6,
                'isActive' => 1
            ],
            [
                'id' => 5,
                'title' => 'Bristol Startup Showcase',
                'description' => 'Local startups pitch their ideas to investors and the Bristol tech community.',
                'eventDate' => '2024-10-05 19:00:00',
                'location' => 'Arnolfini Gallery',
                'maxTickets' => 150,
                'promoterId' => 12,
                'venueId' => 8,
                'isActive' => 1
            ],
            [
                'id' => 6,
                'title' => 'PHP Bristol User Group',
                'description' => 'Monthly PHP meetup with talks, networking, and drinks in central Bristol.',
                'eventDate' => '2024-11-07 18:00:00',
                'location' => 'The Lanes, Clifton',
                'maxTickets' => 60,
                'promoterId' => 10,
                'venueId' => 4,
                'isActive' => 1
            ],
            [
                'id' => 7,
                'title' => 'Bristol Design Festival',
                'description' => 'Celebrating Bristol\'s creative community with workshops, talks, and exhibitions.',
                'eventDate' => '2024-12-12 10:00:00',
                'location' => 'M Shed Museum',
                'maxTickets' => 200,
                'promoterId' => 11,
                'venueId' => 7,
                'isActive' => 1
            ],
            [
                'id' => 8,
                'title' => 'New Year Tech Networking',
                'description' => 'Start the year right with Bristol\'s tech community networking event.',
                'eventDate' => '2025-01-18 18:30:00',
                'location' => 'Passenger Shed',
                'maxTickets' => 80,
                'promoterId' => 9,
                'venueId' => 10,
                'isActive' => 1
            ],
            [
                'id' => 9,
                'title' => 'Bristol Hackathon 2025',
                'description' => '48-hour hackathon focused on solving Bristol\'s urban challenges.',
                'eventDate' => '2025-02-22 09:00:00',
                'location' => 'Engine Shed, Temple Meads',
                'maxTickets' => 120,
                'promoterId' => 16,
                'venueId' => 3,
                'isActive' => 1
            ],
            [
                'id' => 10,
                'title' => 'Harbourside AI Conference',
                'description' => 'Exploring artificial intelligence applications with Bristol researchers and industry.',
                'eventDate' => '2025-03-28 09:30:00',
                'location' => 'Watershed Media Centre',
                'maxTickets' => 180,
                'promoterId' => 16,
                'venueId' => 1,
                'isActive' => 1
            ],
        ];

        $events = $this->table('events');
        $events->insert($data)->saveData();
    }
}
