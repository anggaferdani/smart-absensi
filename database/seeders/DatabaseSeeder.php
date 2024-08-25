<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Profile;
use App\Models\Experience;
use Illuminate\Database\Seeder;
use App\Models\WhyTradersChooseUs;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('123456789'),
                'role' => 1,
            ],
            [
                'name' => 'Angga',
                'email' => 'angga@gmail.com',
                'password' => bcrypt('123456789'),
                'role' => 2,
            ],
        ];

        User::insert($users);
    }
}
