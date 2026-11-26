<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            ['title' => 'Learn PHP', 'done' => true],
            ['title' => 'Learn Laravel', 'done' => false],
            ['title' => 'Build a CRUD API', 'done' => true],
        ];

        if (Task::count() > 0) {
            $this->command->info('Tasks already seeded. Skipping.');
            return;
        } else {
            foreach ($tasks as $task) {
                Task::create($task);
            }
        }

    }
}