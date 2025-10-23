<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_bahan_bakus')
                    ->withPivot('kuantitas_digunakan');
    }
}
