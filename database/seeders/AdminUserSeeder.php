<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@buk.edu.ng'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Create Exam Officer User
        User::firstOrCreate(
            ['email' => 'examofficer@buk.edu.ng'],
            [
                'name' => 'Exam Officer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EXAM_OFFICER,
                'email_verified_at' => now(),
            ]
        );
    }
}
