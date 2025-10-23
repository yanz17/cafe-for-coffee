<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::create([
            'nama' => 'Espresso',
            'deskripsi' => 'Shot kopi murni',
            'harga' => 18000,
            'kategori' => 'Coffee',
        ]);

        Menu::create([
            'nama' => 'Cafe Latte',
            'deskripsi' => 'Espresso dengan susu steamed dan foam tipis',
            'harga' => 30000,
            'kategori' => 'Coffee',
        ]);
        
        Menu::create([
            'nama' => 'Matcha Latte',
            'deskripsi' => 'Bubuk matcha premium dengan susu segar',
            'harga' => 35000,
            'kategori' => 'Non-Coffee',
        ]);
        
        Menu::create([
            'nama' => 'Croissant Cokelat',
            'deskripsi' => 'Pastry renyah isi cokelat',
            'harga' => 25000,
            'kategori' => 'Snack',
        ]);
    }
}