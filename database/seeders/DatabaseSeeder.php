<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ContactPerson;
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
        // $users = [
        //     [
        //         'name' => 'Admin',
        //         'email' => 'admin@smart-absensi.com',
        //         'password' => bcrypt('admin123*'),
        //         'phone' => 123456789,
        //         'profile_picture' => 'default.png',
        //         'role' => 1,
        //     ],
        // ];

        // User::insert($users);

        $contactPersons = [
            [
                'name' => 'Angga',
                'phone' => '6282124151173',
            ],
        ];

        ContactPerson::insert($contactPersons);
    }
}
