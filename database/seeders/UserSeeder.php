<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Normal User
        User::create([
            'user_id'           => 'USR-' . strtoupper(Str::random(6)),
            'name' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => Carbon::now(),
        ]);

        // Admin User
        User::create([
            'user_id'           => 'USR-' . strtoupper(Str::random(6)),
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => Carbon::now(),
        ]);

        // Google Login User (MD Khairul Islam)
        User::create([
            'user_id'           => 'USR-LYB6PS',
            'name'              => 'MD Khairul Islam',
            'email'             => 'mkiuzzal07@gmail.com',
            'password'          => Hash::make('12345678'), // Google user-der password lagena, tobu default ekta diye rakhlam
            'email_verified_at' => Carbon::now(),
            // Jodi apnar database- e avatar ba provider_token field thake, tahole nicher line gulo uncomment kore dite paren:
            // 'avatar'            => 'https://lh3.googleusercontent.com/a/ACg8ocJ9p1Z0V5wemfcTYOYjZOEEMFep3EWw9nijP-K14TNyLC3rTIkj=s96-c',
        ]);
    }
}
