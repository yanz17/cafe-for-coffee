<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Order;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KasirPos extends Component
{
    // State Aplikasi
    public $menus = [];
    public $search = '';

    // State Cart
    public $cart = [];
    public $orderType = 'dine_in';
    public $meja = null;

    // State Pembayaran
    public $paymentMethod = 'Cash';
    public $amountPaid = 0;
    public $changeDue = 0; // Properti untuk menampung nilai kembalian

    protected static string $layout = 'layouts.app'; 

    public function mount()
    {
        $this->loadMenus();
        // Hitung kembalian awal saat mount (akan 0 karena amountPaid=0 dan total=0)
        $this->updatedAmountPaid($this->amountPaid); 
    }

    public function getTotalProperty()
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    /**
     * HOOK PENTING: Dijalankan setiap kali $amountPaid diubah (oleh wire:model.live)
     */
    public function updatedAmountPaid($value)
    {
        $amount = (int) $value;
        $total = $this->getTotalProperty();
        
        // Pastikan amountPaid tidak negatif
        if ($amount < 0) {
            $amount = 0;
            $this->amountPaid = 0; // Reset state jika input negatif
        }

        // Hitung Kembalian
        $this->changeDue = $amount - $total;
    }

    public function loadMenus()
    {
        // Eager load bahanBaku agar Accessor max_stok berfungsi
        $query = Menu::where('is_tersedia', 1)
                     ->with('bahanBaku');

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $this->menus = $query->get();
    }
    
    public function updatedSearch()
    {
        $this->loadMenus();
    }
    
    public function updatedCart($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        
        if (isset($parts[1]) && $parts[1] === 'kuantitas') {
            $item = $this->cart[$index];
            $maxStok = $item['max_stok']; 
            $quantity = (int) $item['kuantitas'];
            
            // 1. Validasi Batas Stok
            if ($quantity > $maxStok) {
                 session()->flash('warning', "Kuantitas {$item['nama']} dibatasi hingga stok maksimum ({$maxStok}).");
                 $quantity = $maxStok;
                 $this->cart[$index]['kuantitas'] = $maxStok;
            } elseif ($quantity < 1) {
                 $quantity = 1;
                 $this->cart[$index]['kuantitas'] = 1;
            }

            // 2. Perhitungan Subtotal
            $this->cart[$index]['subtotal'] = $quantity * $item['harga_satuan'];
            
            // PENTING: Hitung ulang kembalian setelah total berubah
            $this->updatedAmountPaid($this->amountPaid);
        }
    }

    public function increaseQuantity($index)
    {
        $item = $this->cart[$index];
        $maxStok = $item['max_stok'];

        if ($item['kuantitas'] < $maxStok) {
            $this->cart[$index]['kuantitas']++;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['kuantitas'] * $item['harga_satuan'];
        } else {
            session()->flash('warning', "Stok maksimum ({$maxStok}) sudah tercapai.");
        }
        $this->updatedAmountPaid($this->amountPaid); // Hitung ulang kembalian
    }

    public function decreaseQuantity($index)
    {
        $item = $this->cart[$index];
        if ($item['kuantitas'] > 1) {
            $this->cart[$index]['kuantitas']--;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['kuantitas'] * $item['harga_satuan'];
        } else {
            $this->removeItem($index);
        }
        $this->updatedAmountPaid($this->amountPaid); // Hitung ulang kembalian
    }

    public function addToCart(Menu $menu)
    {
        $index = collect($this->cart)->search(fn($item) => $item['menu_id'] === $menu->id);
        $maxStok = $menu->max_stok; 

        if ($index !== false) {
            if ($this->cart[$index]['kuantitas'] >= $maxStok) {
                 session()->flash('error', "Gagal: Stok maksimum ({$maxStok}) untuk {$menu->nama} sudah tercapai.");
                 return;
            }
            
            $this->cart[$index]['kuantitas']++;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['kuantitas'] * $this->cart[$index]['harga_satuan'];
            
        } else {
            if ($maxStok <= 0) {
                 session()->flash('error', "Gagal: {$menu->nama} saat ini tidak tersedia.");
                 return;
            }
            
            $this->cart[] = [
                'menu_id' => $menu->id,
                'nama' => $menu->nama,
                'harga_satuan' => $menu->harga,
                'kuantitas' => 1,
                'subtotal' => $menu->harga,
                'max_stok' => $maxStok, 
            ];
        }
        $this->updatedAmountPaid($this->amountPaid); // Hitung ulang kembalian
    }
    
    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->updatedAmountPaid($this->amountPaid); // Hitung ulang kembalian
    }
    
    // Method untuk mereset state pembayaran
    private function resetPayment()
    {
        $this->amountPaid = 0;
        $this->changeDue = 0;
    }

    // Method utama untuk menyimpan pesanan
    public function storeOrder()
    {
        $total = $this->getTotalProperty();
        $this->validate([
            // Validasi di sisi server harus menggunakan total yang terbaru
            'amountPaid' => 'required|numeric|min:' . $total, 
            'paymentMethod' => 'required|string',
            'orderType' => 'required|in:dine_in,take_away',
            'meja' => 'nullable|string|max:10',
        ]);

        DB::beginTransaction();
        
        try {
            $orderData = [
                'user_id' => null, 
                'nomor_pesanan' => 'POS-' . Str::upper(Str::random(6)) . time(),
                'total_harga' => $total,
                'status_pesanan' => 'diproses', 
                'status_pembayaran' => 'lunas', 
                'tipe_pemesanan' => $this->orderType,
                'meja' => $this->orderType === 'dine_in' ? $this->meja : null,
                'amount_paid' => $this->amountPaid,
                'change_due' => $this->changeDue,
                'payment_method_final' => $this->paymentMethod,
            ];
            $order = Order::create($orderData);

            foreach ($this->cart as $item) {
                $order->items()->create([
                    'menu_id' => $item['menu_id'],
                    'kuantitas' => $item['kuantitas'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
            
            // Panggil fungsi pengurangan stok (dianggap ada di Model Order)
            $order->deductStock(); 
            
            DB::commit();
            
            $this->dispatch('open-invoice-tab', $order->id);

            session()->flash('success', 'Transaksi berhasil diproses. Struk siap dicetak. Kembalian: Rp ' . number_format($this->changeDue, 0, ',', '.') . '.');
            
            // JANGAN gunakan $this->reset() karena akan mereset $menus juga.
            $this->cart = []; 
            $this->amountPaid = 0;
            $this->changeDue = 0;
            $this->meja = null; 
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Panggil loadMenus di sini untuk memastikan $menus selalu terisi saat render
        $this->loadMenus(); 
        $headerContent = view('kasir.pos.header-content'); 
        
        return view('livewire.kasir-pos')
            ->extends('layouts.app')
            ->section('header', $headerContent)
            ->section('content');
    }
}