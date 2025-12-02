<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Wajib: Untuk Auth::user()
use Midtrans\Config;                   // Wajib: Untuk Konfigurasi Midtrans
use Midtrans\Snap;                     // Wajib: Untuk Snap Token

class CustomerController extends Controller
{
        // 1. Tampilkan Daftar Menu
    public function index()
    {
        $menus = Menu::where('is_tersedia', 1)
                   // KOREKSI KRITIS: EAGER LOAD bahanBaku DAN pivot data
                   ->with('bahanBaku') 
                   ->orderBy('kategori')
                   ->orderBy('nama')
                   ->get();
        
        $groupedMenus = $menus->groupBy('kategori');
        $recommendations = $this->getPersonalRecommendations();

        return view('customer.menu.index', [
            'groupedMenus' => $groupedMenus,
            'recommendations' => $recommendations,
        ]);
    }
    
    // Helper untuk Rekomendasi (dianggap ada)
    private function getPersonalRecommendations()
    {
        $userId = auth()->id();
        if (!$userId) {
            return collect(); 
        }
        // Logika rekomendasi diletakkan di sini...
        // Menggunakan logika yang sudah kita buat sebelumnya: mencari item yang kurang populer
        $userTopItems = OrderItem::select('menu_id', DB::raw('SUM(kuantitas) as total_bought'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->where('orders.status_pembayaran', 'lunas')
            ->groupBy('menu_id')
            ->orderByDesc('total_bought')
            ->take(5)
            ->pluck('menu_id')
            ->toArray();

        if (empty($userTopItems)) {
            return collect(); 
        }

        $underperformingItems = OrderItem::select('order_items.menu_id', DB::raw('COUNT(order_items.menu_id) as global_count'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status_pembayaran', 'lunas')
            ->groupBy('order_items.menu_id')
            ->orderBy('global_count', 'asc') 
            ->limit(5)
            ->pluck('order_items.menu_id');

        $recommendedMenus = Menu::with(['bahanBaku' => function($query) { // TAMBAHKAN EAGER LOADING
            $query->select('bahan_bakus.id', 'stok_saat_ini');
        }])
                                 ->whereIn('id', $underperformingItems)
                                 ->whereNotIn('id', $userTopItems) 
                                 ->where('is_tersedia', 1)
                                 ->get();

        if ($recommendedMenus->isEmpty()) {
             $recommendedMenus = Menu::whereIn('id', $underperformingItems)->where('is_tersedia', 1)->get();
        }
        
        return $recommendedMenus;
    }


    // 2. Proses Checkout/Penyimpanan Pesanan
    public function storeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
            'tipe_pemesanan' => 'required|in:dine_in,take_away',
            'meja' => 'nullable|string|max:10',
            'payment_method' => 'required|string', // Kunci Percabangan
        ]);

        $items = json_decode($request->items, true);
        $menuIds = array_column($items, 'menu_id');
        $menuCache = Menu::with(['bahanBaku' => function($query) {
                        $query->select('bahan_bakus.id', 'stok_saat_ini');
                    }])
                    ->whereIn('id', $menuIds)
                    ->get()
                    ->keyBy('id');

        DB::beginTransaction();

        try {
            $totalHarga = 0;
            $orderItems = [];
            
            // 1. Hitung Total dan Siapkan Item
            foreach ($items as $item) { 
                $menuId = $item['menu_id'];
                $kuantitas = (int) $item['kuantitas'];
                
                if (!isset($menuCache[$menuId])) { throw new \Exception("Menu ID #{$menuId} tidak valid."); }
                
                $menu = $menuCache[$menuId];

                $stokTersedia = $menu->max_stok;
                if ($kuantitas > $stokTersedia) {
                    throw new \Exception("Stok {$menu->nama} hanya tersedia {$stokTersedia}. Permintaan {$kuantitas} tidak dapat diproses.");
                }
                if ($stokTersedia === 0) {
                    throw new \Exception("Stok {$menu->nama} sudah habis.");
                }

                $hargaSatuan = $menu->harga;
                $subtotal = $kuantitas * $hargaSatuan;
                $totalHarga += $subtotal;

                $orderItems[] = [
                    'menu_id' => $menuId,
                    'kuantitas' => $kuantitas,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }

            if ($totalHarga === 0) { throw new \Exception("Keranjang kosong."); }

            $user = Auth::user();
            $nomorPesanan = 'WEB-' . Str::upper(Str::random(5)) . time();
            $paymentMethod = $request->payment_method;
            
            $snapToken = null;
            $paymentUrl = null;

            // ===============================================
            // KUNCI PERBAIKAN: PERCABANGAN LOGIKA PEMBAYARAN
            // ===============================================

            if ($paymentMethod === 'Cash' || $paymentMethod === 'Tunai (Bayar Saat Ambil)') {
                // Skenario Tunai (Offline): Langsung pending, tidak panggil Midtrans
                $statusPembayaran = 'menunggu';
                
            } else {
                // Skenario Online (Midtrans): Panggil API
                
                // Siapkan Midtrans Item Detail
                $midtransItemDetails = [];
                foreach ($orderItems as $item) {
                    $midtransItemDetails[] = [
                        'id' => $item['menu_id'],
                        'price' => $item['harga_satuan'],
                        'quantity' => $item['kuantitas'],
                        'name' => $menuCache[$item['menu_id']]->nama,
                    ];
                }

                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); 
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $payload = [
                    'transaction_details' => ['order_id' => $nomorPesanan, 'gross_amount' => $totalHarga],
                    'customer_details' => ['first_name' => $user->name, 'email' => $user->email],
                    'item_details' => $midtransItemDetails, 
                    'enabled_payments' => ['gopay', 'shopeepay', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'other_va', 'qris'],
                ];
                
                // PANGGIL MIDTRANS API
                $snapToken = \Midtrans\Snap::getSnapToken($payload);

                $midtransBaseUrl = env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
                $paymentUrl = "{$midtransBaseUrl}/snap/v2/vtweb/{$snapToken}";
                
                $statusPembayaran = 'menunggu'; // Tetap menunggu update webhook
            }
            // ===============================================
            
            // 3. Buat Order Header
            $order = Order::create([
                'user_id' => auth()->id(),
                'nomor_pesanan' => $nomorPesanan,
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending', 
                'status_pembayaran' => $statusPembayaran, 
                'tipe_pemesanan' => $request->tipe_pemesanan ?? 'take_away', 
                'meja' => $request->meja,
                'snap_token' => $snapToken,        // Null jika tunai
                'payment_url' => $paymentUrl,      // Null jika tunai
            ]);

            $order->items()->createMany($orderItems);

            // 4. Buat Order Items
            foreach ($orderItems as $item) {
                $menu = $menuCache[$item['menu_id']];
                $kuantitasOrder = $item['kuantitas'];

                foreach ($menu->bahanBaku as $bahan) {
                    $kuantitasDigunakan = $bahan->pivot->kuantitas_digunakan * $kuantitasOrder;
                    // Kurangi stok
                    $bahan->stok_saat_ini -= $kuantitasDigunakan;
                    $bahan->save(); // Simpan perubahan stok
                }
            }

            DB::commit();
            
            // Redirect ke detail pesanan (View akan memicu pop-up Midtrans jika token ada)
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan Anda telah dibuat. Silakan selesaikan pembayaran!')
                ->with('clear_cart', true);

        } catch (\Throwable $e) { 
            DB::rollBack();
            // Logging untuk debugging Midtrans/SQL
            \Illuminate\Support\Facades\Log::error("Final Checkout Failure: " . $e->getMessage() . " on line " . $e->getLine());
            
            // Kembalikan error yang ditangkap
            return back()->withInput()->with('error', 'Gagal memproses pesanan! Error: ' . $e->getMessage());
        }
    }
    
    // 3. Tampilkan Riwayat Pesanan
    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->orderBy('created_at', 'desc')
                       ->with('feedback') 
                       ->get();
        
        return view('customer.orders.index', compact('orders'));
    }

    // 4. Tampilkan Detail Pesanan
    public function showOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Eager Load relasi items dan user untuk Midtrans detail
        $order->load(['items.menu', 'user']); 

        return view('customer.orders.show', compact('order'));
    }
    
    public function storeFeedback(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status_pesanan !== 'selesai') {
            abort(403, 'Aksi tidak diizinkan.');
        }
        
        if (\App\Models\Feedback::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'Anda sudah memberikan umpan balik untuk pesanan ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
            'tags' => 'nullable|string', 
        ]);

        $tagsArray = json_decode($request->tags, true);

        \App\Models\Feedback::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
            'tags' => $tagsArray,
        ]);

        return back()->with('success', 'Terima kasih atas umpan baliknya!');
    }
}