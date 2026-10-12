<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Brader Cafe',
                'email' => 'admin@bradercafe.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir Brader Cafe',
                'email' => 'kasir@bradercafe.com',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ],
            [
                'name' => 'Dapur & Barista',
                'email' => 'dapur@bradercafe.com',
                'password' => Hash::make('password'),
                'role' => 'dapur',
            ],
            [
                'name' => 'Owner Brader Cafe',
                'email' => 'owner@bradercafe.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
