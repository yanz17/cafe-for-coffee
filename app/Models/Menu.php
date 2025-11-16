<?php

namespace App\Models;

use App\Models\BahanBaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'foto',
        'harga',
        'kategori',
        'is_tersedia',
    ];

    protected $appends = ['max_stok'];

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'menu_bahan_bakus')
                    ->withPivot('kuantitas_digunakan');
    }

    public function getMaxStokAttribute(): int
    {
        // Jika relasi bahan baku tidak ada, kita harus memuatnya dulu
        if (! $this->relationLoaded('bahanBaku')) {
             // Ini akan memicu N+1 query jika Controller tidak menggunakan with('bahanBaku')
             $this->load('bahanBaku'); 
        }
        
        // Cek jika Menu tidak memiliki resep (seperti menu layanan)
        if ($this->bahanBaku->isEmpty()) {
             // Jika tersedia (1) dan tanpa resep, anggap stok tak terbatas (9999)
             return $this->is_tersedia ? 9999 : 0; 
        }

        $minStok = null;

        foreach ($this->bahanBaku as $bahan) {
            $stokTersedia = (float) $bahan->stok_saat_ini;
            $kuantitasPerPorsi = (float) $bahan->pivot->kuantitas_digunakan;

            if ($kuantitasPerPorsi <= 0 || $stokTersedia <= 0) {
                 // Jika ada bahan baku yang habis atau resepnya 0, langsung 0
                 return 0;
            }

            $maksPorsi = floor($stokTersedia / $kuantitasPerPorsi);

            if ($minStok === null || $maksPorsi < $minStok) {
                 $minStok = $maksPorsi;
            }
        }
        
        return (int) max(0, $minStok ?? 0);
    }
}
