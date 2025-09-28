<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin default
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'          => 'Administrator',
                'phone'         => '+6281234567890',
                'address'       => 'Jl. Admin Street No.1',
                'role'          => 'admin',
                'password_hash' => Hash::make('password123'), // password = password123
            ]
        );

        // Customer default
        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name'          => 'Customer Biasa',
                'phone'         => '+6289876543210',
                'address'       => 'Jl. Customer No.2',
                'role'          => 'customer',
                'password_hash' => Hash::make('password123'), // password = password123
            ]
        );

    }
}
