<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'rating',
        'komentar',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array', // <--- WAJIB: Mengubah array PHP menjadi JSON string saat disimpan
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id', 'id');
    }
}
