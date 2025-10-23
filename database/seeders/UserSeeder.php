<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Manajer
        User::create([
            'name' => 'Manager Cafe',
            'email' => 'manager@mail.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MANAGER, // 'manager'
        ]);

        // 2. Kasir
        User::create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@mail.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_KASIR, // 'kasir'
        ]);
        
        // 3. Pelanggan
        User::create([
            'name' => 'Pelanggan Uji Coba',
            'email' => 'pelanggan@mail.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PELANGGAN, // 'pelanggan'
        ]);

        // Opsional: Beberapa pelanggan dummy
        User::factory(10)->create(['role' => User::ROLE_PELANGGAN]);
    }
}