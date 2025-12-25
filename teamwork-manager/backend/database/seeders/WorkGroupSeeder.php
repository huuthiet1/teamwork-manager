<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkGroup;
use App\Models\User;
use Illuminate\Support\Str;

class WorkGroupSeeder extends Seeder
{
    public function run()
    {
        WorkGroup::truncate();

        $leader = User::where('email', 'leader@test.com')->first();

        WorkGroup::create([
            'name' => 'Nhóm Test Backend',
            'description' => 'Nhóm dùng để test API',
            'group_code' => Str::upper(Str::random(6)),
            'leader_id' => $leader->id
        ]);
    }
}
