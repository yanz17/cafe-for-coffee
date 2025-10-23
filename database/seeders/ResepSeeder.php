<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\BahanBaku;

class ResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $latte = Menu::where('nama', 'Cafe Latte')->first();
        $arabika = BahanBaku::where('nama', 'Biji Kopi Arabika')->first();
        $susu = BahanBaku::where('nama', 'Susu Cair Full Cream')->first();
        $matcha = BahanBaku::where('nama', 'Bubuk Matcha')->first();
        $croissant = BahanBaku::where('nama', 'Croissant Beku')->first();
        
        if ($latte && $arabika && $susu) {
            // Resep Cafe Latte: 18gr Kopi + 150ml Susu
            $latte->bahanBaku()->attach([
                $arabika->id => ['kuantitas_digunakan' => 18],
                $susu->id => ['kuantitas_digunakan' => 150],
            ]);
        }
        
        $matchaLatte = Menu::where('nama', 'Matcha Latte')->first();
        if ($matchaLatte && $matcha && $susu) {
            // Resep Matcha Latte: 15gr Matcha + 150ml Susu
            $matchaLatte->bahanBaku()->attach([
                $matcha->id => ['kuantitas_digunakan' => 15],
                $susu->id => ['kuantitas_digunakan' => 150],
            ]);
        }
        
        $croissantCokelat = Menu::where('nama', 'Croissant Cokelat')->first();
        if ($croissantCokelat && $croissant) {
            // Resep Croissant Cokelat: 1 pcs Croissant Beku
            $croissantCokelat->bahanBaku()->attach([
                $croissant->id => ['kuantitas_digunakan' => 1],
            ]);
        }
    }
}