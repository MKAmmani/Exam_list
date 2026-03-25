<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected static ?string $password;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'Name' => $this->faker->name,
        'Email' => $this->faker->unique()->safeEmail,
        'Date_of_birth' => $this->faker->date(),
        'Contact_address' => $this->faker->address(),
        'Phone_number'=>$this->faker->phoneNumber(),
        'Gender'=>$this->faker->randomElement(['Male','Female']),
        'password'=>static::$password ??= Hash::make('password'),
        //'department_id'=>$this->faker->randomElement(['1','2']),
        //
        // 'level_id'=>$this->faker->randomElement(['1','2',3]),
        ];
    }
}
