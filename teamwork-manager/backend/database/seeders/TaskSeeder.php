<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\WorkGroup;
use App\Models\User;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run()
    {
        Task::truncate();

        $group = WorkGroup::first();
        $leader = User::where('email', 'leader@test.com')->first();

        Task::create([
            'group_id' => $group->id,
            'title' => 'Thiết kế database',
            'description' => 'Thiết kế ERD và bảng dữ liệu',
            'difficulty' => 4,
            'deadline' => Carbon::now()->addDays(3),
            'created_by' => $leader->id
        ]);

        Task::create([
            'group_id' => $group->id,
            'title' => 'Viết API backend',
            'description' => 'Xây dựng REST API',
            'difficulty' => 5,
            'deadline' => Carbon::now()->addDays(5),
            'created_by' => $leader->id
        ]);
    }
}
