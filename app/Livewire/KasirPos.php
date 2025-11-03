<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Order;
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
    public $changeDue = 0;

    // Inisialisasi data saat komponen dimuat
    public function mount()
    {
        $this->loadMenus();
    }

    // Properti terkomputasi (Computed Property) untuk menghitung Total Harga secara real-time
    public function getTotalProperty()
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    // Load Menu dengan filter pencarian
    public function loadMenus()
    {
        // KOREKSI KRITIS: Ganti 'true' dengan angka 1.
        // Ini memastikan filter is_tersedia bekerja di SQLite/database lain.
        $query = Menu::where('is_tersedia', 1); 

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $this->menus = $query->get();
    }
    
    // Dipanggil saat input search berubah (wire:model.live)
    public function updatedSearch()
    {
        $this->loadMenus();
    }

    // Method untuk menambahkan/mengupdate item ke keranjang
    public function addToCart(Menu $menu)
    {
        $index = collect($this->cart)->search(fn($item) => $item['menu_id'] === $menu->id);

        if ($index !== false) {
            $this->cart[$index]['kuantitas']++;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['kuantitas'] * $menu->harga;
        } else {
            $this->cart[] = [
                'menu_id' => $menu->id,
                'nama' => $menu->nama,
                'harga_satuan' => $menu->harga,
                'kuantitas' => 1,
                'subtotal' => $menu->harga,
            ];
        }
        $this->resetPayment(); 
    }
    
    // Method untuk menghitung kembalian saat amountPaid diinput
    public function updatedAmountPaid()
    {
        $this->changeDue = (int)$this->amountPaid - $this->getTotalProperty();
    }

    // Method untuk menghapus item
    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->resetPayment();
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
        // ... (Logic storeOrder Anda yang sudah benar dan lengkap) ...
        // ... (Kode penyimpanan Order dan Order Items tetap sama) ...
        
        // Asumsikan kode penyimpanan di atas sudah dipindahkan ke sini.
        // Jika kode di atas sudah benar, maka:

        // 2. Validasi input pembayaran
        $total = $this->getTotalProperty();
        $this->validate([
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
            
            $order->deductStock(); 
            
            DB::commit();
            
            // 1. DISPATCH EVENT untuk membuka INVOICE di tab baru
            $this->dispatch('open-invoice-tab', $order->id);

            // 2. Beri pesan sukses & Reset state Livewire
            session()->flash('success', 'Transaksi berhasil diproses. Struk siap dicetak. Kembalian: Rp ' . number_format($this->changeDue, 0, ',', '.') . '.');
            $this->reset(['cart', 'amountPaid', 'changeDue', 'meja']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    // KOREKSI: Tambahkan method render() yang diperlukan Livewire Page Component
    public function render()
    {
        // Eager load data menus di sini untuk memastikan ketersediaan data di view
        $this->loadMenus(); 
        
        // Kita buat view header terpisah untuk dimasukkan ke @section('header')
        // Asumsi: View header berada di resources/views/kasir/pos/header-content.blade.php
        $headerContent = view('kasir.pos.header-content'); 
        
        // KOREKSI FINAL: Menggunakan chain call extends/section
        return view('livewire.kasir-pos')
            ->extends('layouts.app')
            ->section('header', $headerContent) // Menyuntikkan judul
            ->section('content'); // Menyuntikkan konten POS ke main body
    }
}