<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', // Ini diisi otomatis oleh relasi, tapi baik untuk dimasukkan
        'menu_id', // <--- WAJIB DITAMBAHKAN (SESUAI DENGAN ERROR)
        'kuantitas',
        'harga_satuan',
        'subtotal',
        'catatan', // Jika Anda menggunakan catatan
    ];

    // Relasi ke Order (Wajib)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Menu (Wajib)
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}