<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activity_date' => $this->faker->date(),
            'check_in' => $this->faker->dateTime(),
            'check_out' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement(['present', 'absent', 'late']),
            'note' => $this->faker->sentence(),
        ];
    }
} 