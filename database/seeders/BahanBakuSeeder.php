<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BahanBaku::create([
            'nama' => 'Biji Kopi Arabika',
            'unit' => 'gram',
            'stok_saat_ini' => 50000, // 50 kg
            'stok_minimal' => 5000,
        ]);
        
        BahanBaku::create([
            'nama' => 'Susu Cair Full Cream',
            'unit' => 'ml',
            'stok_saat_ini' => 100000, // 100 liter
            'stok_minimal' => 10000,
        ]);
        
        BahanBaku::create([
            'nama' => 'Bubuk Matcha',
            'unit' => 'gram',
            'stok_saat_ini' => 10000, // 10 kg
            'stok_minimal' => 1000,
        ]);
        
        BahanBaku::create([
            'nama' => 'Croissant Beku',
            'unit' => 'pcs',
            'stok_saat_ini' => 50, 
            'stok_minimal' => 10,
        ]);
    }
}