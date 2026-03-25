<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = [
            [
                'name' => 'LT1 (Lecture Theatre 1)',
                'type' => 'lecture_hall',
                'capacity' => 100,
                'location' => 'Faculty of Computing Building, Ground Floor',
                'description' => 'Main lecture theatre with projector and sound system',
                'is_active' => true,
            ],
            [
                'name' => 'LT2 (Lecture Theatre 2)',
                'type' => 'lecture_hall',
                'capacity' => 80,
                'location' => 'Faculty of Computing Building, Ground Floor',
                'description' => 'Second lecture theatre with whiteboard',
                'is_active' => true,
            ],
            [
                'name' => 'LT3 (Lecture Theatre 3)',
                'type' => 'lecture_hall',
                'capacity' => 60,
                'location' => 'Faculty of Computing Building, First Floor',
                'description' => 'Third lecture theatre',
                'is_active' => true,
            ],
            [
                'name' => 'Room 101',
                'type' => 'classroom',
                'capacity' => 50,
                'location' => 'Faculty of Computing Building, First Floor',
                'description' => 'Standard classroom',
                'is_active' => true,
            ],
            [
                'name' => 'Room 102',
                'type' => 'classroom',
                'capacity' => 50,
                'location' => 'Faculty of Computing Building, First Floor',
                'description' => 'Standard classroom',
                'is_active' => true,
            ],
            [
                'name' => 'Room 201',
                'type' => 'classroom',
                'capacity' => 40,
                'location' => 'Faculty of Computing Building, Second Floor',
                'description' => 'Standard classroom',
                'is_active' => true,
            ],
            [
                'name' => 'Room 202',
                'type' => 'classroom',
                'capacity' => 40,
                'location' => 'Faculty of Computing Building, Second Floor',
                'description' => 'Standard classroom',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Lab 1',
                'type' => 'classroom',
                'capacity' => 30,
                'location' => 'Faculty of Computing Building, Ground Floor',
                'description' => 'Computer laboratory with 30 workstations',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Lab 2',
                'type' => 'classroom',
                'capacity' => 30,
                'location' => 'Faculty of Computing Building, Ground Floor',
                'description' => 'Computer laboratory with 30 workstations',
                'is_active' => true,
            ],
            [
                'name' => 'Conference Hall',
                'type' => 'hall',
                'capacity' => 200,
                'location' => 'Faculty of Computing Building, Ground Floor',
                'description' => 'Large conference hall for events and exams',
                'is_active' => true,
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}
