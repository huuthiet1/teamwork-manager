<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::truncate();

        User::create([
            'name' => 'Leader User',
            'email' => 'leader@test.com',
            'password' => Hash::make('123456'),
            'is_active' => 1
        ]);

        User::create([
            'name' => 'Member One',
            'email' => 'member1@test.com',
            'password' => Hash::make('123456'),
            'is_active' => 1
        ]);

        User::create([
            'name' => 'Member Two',
            'email' => 'member2@test.com',
            'password' => Hash::make('123456'),
            'is_active' => 1
        ]);
    }
}
