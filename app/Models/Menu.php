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

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'menu_bahan_bakus')
                    ->withPivot('kuantitas_digunakan');
    }
    
    // Wajib: Gunakan ini agar kolom "max_stok" selalu tersedia saat diakses
    protected $appends = ['max_stok']; 

    /**
     * Accessor untuk menghitung batas maksimal Menu yang dapat dibuat
     * berdasarkan Bahan Baku yang tersedia (Bottleneck Ingredient).
     */
    public function getMaxStokAttribute(): int
    {
        // PENTING: Jika Accessor dipanggil, pastikan relasi bahanBaku dimuat
        if (! $this->relationLoaded('bahanBaku')) {
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

            // Jika kuantitas yang digunakan adalah 0 atau bahan baku habis, kita anggap 0
            if ($kuantitasPerPorsi <= 0 || $stokTersedia <= 0) {
                 return 0;
            }

            // Hitung berapa banyak porsi menu ini yang bisa dibuat
            $maksPorsi = floor($stokTersedia / $kuantitasPerPorsi);

            if ($minStok === null || $maksPorsi < $minStok) {
                 $minStok = $maksPorsi;
            }
        }
        
        return (int) max(0, $minStok ?? 0);
    }
    
    // CATATAN: Method deductStock yang dipanggil di OrderController harus di Model Order, 
    // atau Anda harus memiliki fungsi di sini yang dipanggil dari OrderController.
    // Asumsi: deductStock() ada di Model Order.
}