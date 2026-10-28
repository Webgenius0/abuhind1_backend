<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define user data for seeding
        $userData = [
            [
                'user_id'           => 'USR-ADM001',
                'name'              => 'admin',
                'email'             => 'admin@gmail.com',
                'password'          => Hash::make('12345678'),
                'role'              => 'Admin',
                'status'            => 'active',
                'is_premium'        => true,
                'is_18_plus'        => true,
                'gender'            => 'Male',
                'background'        => 'Standard',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'user_id'           => 'USR-USR001',
                'name'              => 'user',
                'email'             => 'user@gmail.com',
                'password'          => Hash::make('12345678'),
                'role'              => 'User',
                'status'            => 'active',
                'is_premium'        => true,
                'is_18_plus'        => true,
                'gender'            => 'Male',
                'background'        => 'Standard',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'user_id'           => 'USR-JHN002',
                'name'              => 'john doe',
                'email'             => 'user2@gmail.com',
                'password'          => Hash::make('12345678'),
                'role'              => 'User',
                'status'            => 'active',
                'is_premium'        => true,
                'is_18_plus'        => true,
                'gender'            => 'Male',
                'background'        => 'Standard',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'user_id'           => 'USR-LYB6PS',
                'name'              => 'MD Khairul Islam',
                'email'             => 'mkiuzzal07@gmail.com',
                'password'          => Hash::make('12345678'),
                'role'              => 'User',
                'status'            => 'active',
                'is_premium'        => false,
                'is_18_plus'        => true,
                'gender'            => 'Male',
                'background'        => 'Asian',
                'email_verified_at' => Carbon::now(),
            ],
        ];

        // User data array loop to create users
        foreach ($userData as $data) {
            User::create($data);
        }
    }
}
