<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BahanBaku extends Model
{
    use HasFactory; // Gunakan trait ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 
        'unit',
        'stok_saat_ini',
        'stok_minimal',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_bahan_bakus')
                    ->withPivot('kuantitas_digunakan');
    }
}
