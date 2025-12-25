<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\WorkGroup;

class GroupMemberSeeder extends Seeder
{
    public function run()
    {
        GroupMember::truncate();

        $group = WorkGroup::first();
        $leader = User::where('email', 'leader@test.com')->first();

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $leader->id,
            'role' => 'leader'
        ]);

        User::where('email', '!=', 'leader@test.com')->get()
            ->each(function ($user) use ($group) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'role' => 'member'
                ]);
            });
    }
}
