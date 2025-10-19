<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Seeder untuk membuat data awal pengguna (admin dan customer)
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'          => 'Administrator', // Nama admin default
                'phone'         => '+6281234567890', // Nomor telepon admin
                'address'       => 'Jl. Admin Street No.1', // Alamat admin
                'role'          => 'admin', // Peran pengguna: admin
                'password_hash' => Hash::make('password123'), // Password default admin
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name'          => 'Customer Biasa', // Nama customer default
                'phone'         => '+6289876543210', // Nomor telepon customer
                'address'       => 'Jl. Customer No.2', // Alamat customer
                'role'          => 'customer', // Peran pengguna: customer
                'password_hash' => Hash::make('password123'), // Password default customer
            ]
        );
        // Panggil seeder tambahan
        $this->call([
            MediaSeeder::class,
        ]);
    }
}
