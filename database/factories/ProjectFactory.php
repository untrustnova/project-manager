<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'project_director' => User::factory(),
            'project_name' => $this->faker->words(3, true),
            'start_date' => $this->faker->date(),
            'deadline' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
            'level' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'status' => $this->faker->randomElement(['ongoing', 'completed', 'pending']),
        ];
    }
} 