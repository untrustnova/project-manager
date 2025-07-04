<?php

namespace Database\Factories;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    public function definition(): array
    {
        return [
            'leave_category' => $this->faker->randomElement(['sick', 'annual', 'unpaid']),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            'description' => $this->faker->sentence(),
            'bring_laptop' => $this->faker->boolean(),
            'still_be_contacted' => $this->faker->boolean(),
            'submitted_by_user_id' => User::factory(),
        ];
    }
} 