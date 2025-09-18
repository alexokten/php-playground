<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class VenueSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            ['name' => 'Watershed Media Centre', 'location' => 'Harbourside, Bristol', 'maxCapacity' => 300, 'isActive' => 1],
            ['name' => 'Bristol Old Vic Theatre', 'location' => 'King Street, Bristol', 'maxCapacity' => 548, 'isActive' => 1],
            ['name' => 'Engine Shed', 'location' => 'Temple Meads, Bristol', 'maxCapacity' => 1500, 'isActive' => 1],
            ['name' => 'The Lanes Bowling Alley', 'location' => 'Clifton, Bristol', 'maxCapacity' => 200, 'isActive' => 1],
            ['name' => 'St Pauls Learning Centre', 'location' => 'St Pauls, Bristol', 'maxCapacity' => 150, 'isActive' => 1],
            ['name' => 'Merchant Venturers Building', 'location' => 'University of Bristol', 'maxCapacity' => 400, 'isActive' => 1],
            ['name' => 'M Shed Museum', 'location' => 'Wapping Wharf, Bristol', 'maxCapacity' => 250, 'isActive' => 1],
            ['name' => 'Arnolfini Gallery', 'location' => 'Narrow Quay, Bristol', 'maxCapacity' => 180, 'isActive' => 1],
            ['name' => 'Spike Island', 'location' => 'Cumberland Road, Bristol', 'maxCapacity' => 120, 'isActive' => 1],
            ['name' => 'Passenger Shed', 'location' => 'Avon Street, Bristol', 'maxCapacity' => 80, 'isActive' => 1],
        ];

        $venues = $this->table('venues');
        $venues->insert($data)->saveData();
    }
}
