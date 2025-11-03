<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Feedback;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // 1. Tampilkan Daftar Menu
    public function index()
    {
        // Ambil semua menu yang tersedia dan urutkan berdasarkan kategori
        $menus = \App\Models\Menu::where('is_tersedia', true)
                                ->orderBy('kategori')
                                ->orderBy('nama')
                                ->get();
        
        // KOREKSI UTAMA: Kelompokkan menu berdasarkan kategori
        $groupedMenus = $menus->groupBy('kategori');

        $recommendations = $this->getPersonalRecommendations();

        return view('customer.menu.index', [
            'groupedMenus' => $groupedMenus, // MENGGANTI $menus
            'recommendations' => $recommendations,
        ]);
    }

    private function getPersonalRecommendations()
    {
        $userId = auth()->id();
        if (!$userId) {
            return collect(); 
        }

        // A. Cari 5 item menu yang paling sering dibeli oleh pengguna ini (User Top Items)
        //    Query ini tetap dibutuhkan untuk menentukan apa yang TIDAK perlu direkomendasikan.
        $userTopItems = \App\Models\OrderItem::select('menu_id', DB::raw('SUM(kuantitas) as total_bought'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->where('orders.status_pembayaran', 'lunas')
            ->groupBy('menu_id')
            ->orderByDesc('total_bought')
            ->take(5)
            ->pluck('menu_id')
            ->toArray();

        // B. Query Baru: Cari item yang PALING SEDIKIT dibeli secara global (Underperforming Items)
        $underperformingItems = \App\Models\OrderItem::select('order_items.menu_id', DB::raw('COUNT(order_items.menu_id) as global_count'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status_pembayaran', 'lunas')
            ->groupBy('order_items.menu_id')
            
            // KUNCI PERBAIKAN: Urutkan secara ASCENDING (paling sedikit dibeli)
            ->orderBy('global_count', 'asc') 
            
            ->limit(5)
            ->pluck('order_items.menu_id');

        // C. Ambil detail menu untuk rekomendasi
        $recommendedMenus = \App\Models\Menu::whereIn('id', $underperformingItems)
                                        
                                        // KOREKSI: JANGAN merekomendasikan item yang sudah menjadi favorit
                                        // Kita filter di sini agar lebih fleksibel
                                        ->whereNotIn('id', $userTopItems) 
                                        
                                        // Final check: Pastikan menu tersedia
                                        ->where('is_tersedia', true)
                                        ->get();

        // Jika rekomendasi masih kosong, berikan menu paling sedikit dibeli (tanpa pengecualian favorit)
        if ($recommendedMenus->isEmpty() && !empty($userTopItems)) {
            $recommendedMenus = \App\Models\Menu::whereIn('id', $underperformingItems)
                                                ->where('is_tersedia', true)
                                                ->get();
        }
        
        return $recommendedMenus;
    }

    // 2. Proses Checkout/Penyimpanan Pesanan
    public function storeOrder(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'items' => 'required|string',
            // HAPUS VALIDASI payment_method, tipe_pemesanan, meja untuk sementara 
            // untuk mengisolasi error di Mass Assignment utama.
        ]);

        $items = json_decode($request->items, true);
        
        // Pastikan menu ID yang dikirim ada di database.
        $menuIds = array_column($items, 'menu_id');
        $menuCache = \App\Models\Menu::whereIn('id', $menuIds)->pluck('harga', 'id');

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $totalHarga = 0;
            $orderItems = [];

            // 2. Hitung Total dan Siapkan Item
            foreach ($items as $item) { 
                $menuId = $item['menu_id'];
                $kuantitas = (int) $item['kuantitas'];
                
                // Cek Kritis: Pastikan Menu ID ada di cache (memperbaiki findOrFail)
                if (!isset($menuCache[$menuId])) {
                    throw new \Exception("Menu ID #{$menuId} tidak valid.");
                }
                
                $hargaSatuan = $menuCache[$menuId];
                $subtotal = $kuantitas * $hargaSatuan;
                $totalHarga += $subtotal;

                $orderItems[] = [
                    'menu_id' => $menuId,
                    'kuantitas' => $kuantitas,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }

            if ($totalHarga === 0) {
                throw new \Exception("Keranjang kosong.");
            }

            // 3. Buat Order Header
            $order = \App\Models\Order::create([
                'user_id' => auth()->id(),
                'nomor_pesanan' => 'WEB-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(5)) . time(),
                'total_harga' => $totalHarga,
                'status_pesanan' => 'pending', 
                'status_pembayaran' => 'menunggu',
                // Gunakan nilai default/safe karena validasi di-skip
                'tipe_pemesanan' => $request->tipe_pemesanan ?? 'take_away', 
                'meja' => $request->meja,
            ]);

            // 4. Buat Order Items
            foreach ($orderItems as $item) {
                $order->items()->create($item); 
            }

            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan Anda telah dibuat!')
                ->with('clear_cart', true);

        } catch (\Throwable $e) { // Menggunakan Throwable untuk menangkap fatal error
            \Illuminate\Support\Facades\DB::rollBack();
            
            // Cek log untuk pesan error ini!
            \Illuminate\Support\Facades\Log::error("Final Checkout Failure: " . $e->getMessage() . " on line " . $e->getLine());
            
            // Halaman akan di-redirect dengan pesan error (jika log tidak muncul)
            return back()->withInput()->with('error', 'Gagal memproses pesanan! Error: ' . $e->getMessage());
        }
    }
    
    // 3. Tampilkan Riwayat Pesanan
    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->orderBy('created_at', 'desc')
                       //->with('feedback')
                       ->get();
        return view('customer.orders.index', compact('orders'));
    }

    // 4. Tampilkan Detail Pesanan
    public function showOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Eager Load Relasi Feedback (Penting!)
        $order->load(['items.menu']); // <-- Tambahkan 'feedback' di sini!
        
        // Jika relasi tidak di-eager load, Blade di view akan memicu query yang crash.
        
        return view('customer.orders.show', compact('order'));
    }

    public function storeFeedback(Request $request, Order $order)
    {
        // 1. Validasi Kepemilikan dan Status
        if ($order->user_id !== auth()->id() || $order->status_pesanan !== 'selesai') {
            abort(403, 'Aksi tidak diizinkan.');
        }
        
        // 2. Cek apakah feedback sudah ada
        if (\App\Models\Feedback::where('order_id', $order->id)->exists()) { // <-- Pastikan namespace benar
                return back()->with('error', 'Anda sudah memberikan umpan balik untuk pesanan ini.');
            }

        // 3. Validasi Input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        // 4. Buat Feedback
        Feedback::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Terima kasih atas umpan baliknya!');
    }
}
