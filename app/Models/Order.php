<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BahanBaku;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // WAJIB ada, meskipun sering diisi null/kasir
        'nomor_pesanan',
        'total_harga',
        'status_pesanan',
        'status_pembayaran',
        'tipe_pemesanan',
        'meja',
        // Kolom Pembayaran dari Livewire POS
        'amount_paid',
        'change_due',
        'payment_method_final',
        'snap_token', // <-- Tambahkan ini
        'payment_url', // <-- Tambahkan ini
    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deductStock()
    {
        // Pastikan relasi items sudah dimuat
        $this->loadMissing('items.menu.bahanBaku'); 

        // Loop melalui setiap item pesanan
        foreach ($this->items as $orderItem) {
            $menu = $orderItem->menu;
            $orderQuantity = $orderItem->kuantitas;

            // Loop melalui setiap bahan baku dalam resep menu
            foreach ($menu->bahanBaku as $bahanBaku) {
                // Kuantitas bahan baku yang digunakan per menu
                $recipeQuantity = $bahanBaku->pivot->kuantitas_digunakan;
                
                // Total pengurangan yang dibutuhkan: kuantitas resep * kuantitas pesanan
                $deductionAmount = $recipeQuantity * $orderQuantity;

                // Lakukan pengurangan stok
                BahanBaku::where('id', $bahanBaku->id)->update([
                    // Gunakan DB::raw untuk menghindari masalah race condition saat update stok
                    'stok_saat_ini' => DB::raw("stok_saat_ini - $deductionAmount")
                ]);
            }
        }
    }

    public function feedback()
    {
        return $this->hasOne(\App\Models\Feedback::class, 'order_id', 'id');
    }


    /**
     * Memverifikasi apakah ada cukup stok bahan baku untuk semua item dalam keranjang.
     * @param array $cartData Array keranjang dari Livewire ({menu_id, kuantitas, ...})
     * @return bool|string True jika stok cukup, string jika ada bahan baku yang kurang.
     */
    public static function checkStockAvailability(array $cartData)
    {
        // Kumpulkan total kebutuhan bahan baku dari seluruh keranjang
        $requiredIngredients = [];
        
        // 1. Load semua resep untuk semua menu di keranjang
        $menuIds = array_column($cartData, 'menu_id');
        $menus = Menu::with('bahanBaku')->whereIn('id', $menuIds)->get()->keyBy('id');

        foreach ($cartData as $item) {
            $menuId = $item['menu_id'];
            $orderQuantity = $item['kuantitas'];

            if (!isset($menus[$menuId])) {
                return "Menu ID #{$menuId} tidak ditemukan.";
            }

            $menu = $menus[$menuId];
            
            // 2. Kalkulasi total kebutuhan
            foreach ($menu->bahanBaku as $bahanBaku) {
                $recipeQuantity = $bahanBaku->pivot->kuantitas_digunakan;
                $requiredAmount = $recipeQuantity * $orderQuantity;
                $bahanBakuId = $bahanBaku->id;
                $bahanBakuUnit = $bahanBaku->unit;

                // Tambahkan ke total kebutuhan
                if (!isset($requiredIngredients[$bahanBakuId])) {
                    $requiredIngredients[$bahanBakuId] = [
                        'nama' => $bahanBaku->nama,
                        'unit' => $bahanBakuUnit,
                        'needed' => 0,
                        'available' => $bahanBaku->stok_saat_ini,
                    ];
                }
                $requiredIngredients[$bahanBakuId]['needed'] += $requiredAmount;
            }
        }
        
        // 3. Bandingkan Kebutuhan dengan Stok Tersedia
        $shortageMessage = [];
        foreach ($requiredIngredients as $id => $req) {
            if ($req['needed'] > $req['available']) {
                $needed = number_format($req['needed'], 2);
                $available = number_format($req['available'], 2);
                $shortage = number_format($req['needed'] - $req['available'], 2);
                
                $shortageMessage[] = "Stok **{$req['nama']}** kurang: Tersedia {$available} {$req['unit']}, Dibutuhkan {$needed} {$req['unit']} (Kurang {$shortage} {$req['unit']}).";
            }
        }

        if (!empty($shortageMessage)) {
            return implode(" | ", $shortageMessage);
        }

        return true; // Stok cukup
    }
}
