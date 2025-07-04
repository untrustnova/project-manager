<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user utama
        $mainUser = \App\Models\User::factory()->create([
            'name' => 'Sunaookami Shiroko',
            'email' => 'sunaookamishiroko@abydos.sch',
            'role' => 'employee',
        ]);

        // Buat beberapa user lain
        \App\Models\User::factory(9)->create();

        // Buat project yang pasti ada relasi ke user utama
        $mainProject = \App\Models\Project::factory()->create([
            'project_director' => $mainUser->user_id,
            'project_name' => 'Project Shiroko',
            'level' => 'easy',
            'status' => 'ongoing',
        ]);
        \App\Models\Project::factory(4)->create();

        // Buat task yang assigned ke user utama dan project utama
        \App\Models\Task::factory()->create([
            'project_id' => $mainProject->project_id,
            'task_name' => 'Task Shiroko',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_user_id' => $mainUser->user_id,
        ]);
        \App\Models\Task::factory(19)->create();

        // Buat activity untuk user utama
        \App\Models\Activity::factory()->create([
            'user_id' => $mainUser->user_id,
            'status' => 'present',
        ]);
        \App\Models\Activity::factory(14)->create();

        // Buat leave untuk user utama
        \App\Models\Leave::factory()->create([
            'submitted_by_user_id' => $mainUser->user_id,
        ]);
        \App\Models\Leave::factory(7)->create();
    }
}
