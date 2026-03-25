<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::factory(20)->create([
            //'department_id' => 1, // Assuming you want to assign all students to the first department
            // 'level_id' => 1, // Uncomment if you have a level_id field and want to set it
        ]);
    }
}
