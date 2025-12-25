<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskAssignment;
use App\Models\Task;
use App\Models\User;

class TaskAssignmentSeeder extends Seeder
{
    public function run()
    {
        TaskAssignment::truncate();

        $tasks = Task::all();
        $members = User::where('email', '!=', 'leader@test.com')->get();
        $leader = User::where('email', 'leader@test.com')->first();

        foreach ($tasks as $task) {
            foreach ($members as $member) {
                TaskAssignment::create([
                    'task_id' => $task->id,
                    'user_id' => $member->id,
                    'assigned_by' => $leader->id
                ]);
            }
        }
    }
}
