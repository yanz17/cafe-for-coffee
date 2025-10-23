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
        'harga',
        'kategori',
        'is_tersedia',
    ];

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'menu_bahan_bakus')
                    ->withPivot('kuantitas_digunakan');
    }
}
