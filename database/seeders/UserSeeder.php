<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'test@example.com',
            'password' => Hash::make('test'),
            'name' => 'test',
            'preferred_activity' => 'hiking',
            'preferred_travel_style' => 'backpacking',
            'home_location' => 'Jakarta',
        ]);

        User::create([
            'email' => 'test@example2.com',
            'password' => Hash::make('test2'),
            'name' => 'test2',
            'preferred_activity' => 'swimming',
            'preferred_travel_style' => 'luxury',
            'home_location' => 'New York',
        ]);
    }
}
