<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Filter berdasarkan nomor pesanan (kode/scan)
        $search = $request->input('search');
        
        // Pesanan yang menunggu konfirmasi pembayaran (hanya dari online/web)
        $pendingOrders = Order::where('status_pembayaran', 'menunggu')
                            ->orderBy('created_at', 'asc');
        
        // Pesanan aktif (sudah lunas, sedang diproses)
        $activeOrders = Order::whereIn('status_pesanan', ['diproses', 'siap_ambil'])
                            ->orderBy('created_at', 'asc');
        
        $completedOrders = Order::where('status_pesanan', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->limit(20);

        // Jika ada pencarian, filter kedua query
        if ($search) {
                $filter = function ($query) use ($search) {
                    $query->where('nomor_pesanan', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                };
            $pendingOrders->where($filter);
            $activeOrders->where($filter);
            $completedOrders->where($filter);
        }

        return view('kasir.orders.index', [
            'pendingOrders' => $pendingOrders->get(),
            'activeOrders' => $activeOrders->get(),
            'completedOrders' => $completedOrders->with('user')->get(),
            'search' => $search
        ]);
    }
    
    public function create()
    {
        // Tampilan antarmuka POS: menampilkan menu dan keranjang
        $menus = Menu::where('is_tersedia', true)->get();
        return view('kasir.pos.create', compact('menus'));
    }

    public function store(Request $request)
    {
        // Logika pembuatan pesanan oleh Kasir

        // 1. Validasi input (minimal harus ada item)
        $request->validate([
            'items' => 'required|array', // array of {menu_id, quantity}
            'tipe_pemesanan' => 'required|in:dine_in,take_away,online',
            'meja' => 'nullable|string|max:10', // Jika dine_in
        ]);

        DB::beginTransaction();

        try {
            $totalHarga = 0;
            $orderItems = [];

            // 2. Ambil data menu dan hitung total
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $kuantitas = $item['kuantitas'];
                $subtotal = $kuantitas * $menu->harga;
                $totalHarga += $subtotal;

                $orderItems[] = [
                    'menu_id' => $menu->id,
                    'kuantitas' => $kuantitas,
                    'harga_satuan' => $menu->harga,
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ];
            }

            // 3. Buat Order Header
            $order = Order::create([
                'user_id' => $request->user_id ?? null, // Null jika pelanggan datang langsung
                'nomor_pesanan' => 'CC-' . Str::upper(Str::random(6)) . time(), // Contoh nomor unik
                'total_harga' => $totalHarga,
                'status_pesanan' => 'diproses', // Pesanan Kasir biasanya langsung diproses
                'status_pembayaran' => 'menunggu', // Pembayaran akan dicatat terpisah
                'tipe_pemesanan' => $request->tipe_pemesanan,
                'meja' => $request->meja,
            ]);

            // 4. Buat Order Items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            $invoiceUrl = route('kasir.orders.invoice', $order);

            DB::commit();

            return redirect()->route('kasir.orders.index')
                            ->with('success', 'Pesanan #' . $order->nomor_pesanan . ' berhasil dibuat. Lanjutkan ke Pembayaran.')
                            ->with('print_invoice_url', $invoiceUrl);;

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function confirmPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount_paid' => 'required|integer|min:' . $order->total_harga, // Harus lebih besar atau sama dengan total
            'payment_method_final' => 'required|string|max:50',
        ]);
        
        // Pastikan pesanan memang menunggu pembayaran
        if ($order->status_pembayaran !== 'menunggu') {
            return back()->with('error', 'Pembayaran sudah dilunasi.');
        }

        $amountPaid = (int) $request->amount_paid;
        $changeDue = $amountPaid - $order->total_harga; // Hitung kembalian

        $order->update([
            'status_pembayaran' => 'lunas',
            'status_pesanan' => 'diproses', // Ubah status pesanan menjadi diproses
            'amount_paid' => $amountPaid,
            'change_due' => $changeDue,
            'payment_method_final' => $request->payment_method_final,
        ]);

        return back()->with('success', 
            'Pembayaran #' . $order->nomor_pesanan . ' LUNAS (' . $request->payment_method_final . '). Kembalian: Rp ' . number_format($changeDue, 0, ',', '.')
        );
    }

    public function completeOrder(Order $order)
    {
        // 1. Validasi Status
        if ($order->status_pembayaran !== 'lunas' || $order->status_pesanan !== 'diproses') {
            return back()->with('error', 'Pesanan harus lunas dan diproses sebelum diselesaikan.');
        }
        
        DB::beginTransaction();
        
        try {
            // 2. Kurangi Stok Bahan Baku
            $order->deductStock(); 
            
            // 3. Ubah Status Pesanan menjadi Selesai
            $order->update([
                'status_pesanan' => 'selesai',
            ]);

            DB::commit();
            
            // KOREKSI A (RETURN SUKSES): Redirect ke Invoice
            return redirect()->back()
                            ->with('success', 'Pesanan ' . $order->nomor_pesanan. ' selesai.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // KOREKSI B (RETURN ERROR): Tampilkan pesan error dan kembali
            // Logging error agar Manajer bisa cek
            \Illuminate\Support\Facades\Log::error("Stock Deduction Failed for Order #" . $order->id . ": " . $e->getMessage());
            
            // Kembalikan ke halaman daftar pesanan dengan pesan error
            return back()->with('error', 'Gagal menyelesaikan pesanan. Stok tidak cukup atau terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function revertOrder(Order $order)
    {
        // Cek apakah pesanan benar-benar selesai sebelum dibatalkan statusnya
        if ($order->status_pesanan !== 'selesai') {
            return back()->with('error', 'Pesanan tidak dalam status Selesai.');
        }

        // Ubah status ke 'diproses' (atau status aktif default Anda)
        $order->update([
            'status_pesanan' => 'diproses', 
        ]);
        
        // CATATAN: Kuantitas stok tidak bisa dikembalikan di sini karena Order tidak memiliki fungsi undo deductStock.
        // Asumsi: Karena ini adalah mitigasi kesalahan Kasir, stok memang sudah terpotong dan dibiarkan.

        return back()->with('success', 'Status pesanan #' . $order->nomor_pesanan . ' berhasil dikembalikan ke Diproses.');
    }

    public function productPopularity(Request $request)
    {
        // Filter tanggal (opsional)
        $startDate = $request->input('start_date', now()->subMonths(1)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $popularity = OrderItem::select(
            'menus.nama', 
            'menus.kategori', 
            DB::raw('SUM(order_items.kuantitas) as total_sold')
        )
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('menus', 'order_items.menu_id', '=', 'menus.id')
        // Hanya hitung pesanan yang sudah lunas
        ->where('orders.status_pembayaran', 'lunas')
        ->whereDate('orders.created_at', '>=', $startDate)
        ->whereDate('orders.created_at', '<=', $endDate)
        // Kelompokkan dan urutkan berdasarkan kuantitas terjual
        ->groupBy('menus.nama', 'menus.kategori')
        ->orderByDesc('total_sold')
        ->get();
        
        // Agregasi Total Penjualan per Kategori (Roll-up)
        $categorySummary = $popularity->groupBy('kategori')->map(function ($items) {
            return [
                'total_items' => $items->sum('total_sold'),
                'item_count' => $items->count(),
            ];
        })->sortByDesc('total_items');


        return view('manager.reports.popularity', compact(
            'popularity', 
            'categorySummary',
            'startDate', 
            'endDate'
        ));
    }

    public function processOrder(Order $order)
    {

        // Cek Kritis: Pastikan pesanan sedang menunggu pembayaran
        if ($order->status_pembayaran !== 'menunggu') {
            return back()->with('error', 'Pesanan ini sudah diproses atau dibatalkan.');
        }
        
        DB::beginTransaction();
        try {
            $order->update([
                'status_pembayaran' => 'lunas',
                'status_pesanan' => 'diproses', 
                'payment_method_final' => $order->payment_method_final ?? 'Online/Konfirmasi Kasir',
            ]);
            
            DB::commit();

            // KUNCI: Siapkan URL Invoice untuk flash session
            $invoiceUrl = route('kasir.orders.invoice', $order);

            // Redirect kembali ke index dengan sinyal cetak
            return back()
                ->with('success', 'Pesanan berhasil dikonfirmasi dan diproses. Struk siap dicetak.')
                ->with('print_invoice_url', $invoiceUrl); // URL untuk JS global

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Processing Failed: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan view Invoice/Struk untuk pesanan yang sudah selesai.
     */
    public function showInvoice(\App\Models\Order $order)
    {
        // 1. Amankan Akses: Hanya pesanan lunas dan selesai yang seharusnya dicetak
        if ($order->status_pembayaran !== 'lunas') {
            return redirect()->back()->with('error', 'Invoice hanya tersedia untuk pesanan yang sudah lunas.');
        }

        // 2. Eager Load data yang dibutuhkan: Items, Menu, dan User (Pelanggan/Kasir)
        $order->load(['items.menu', 'user']);

        // 3. Tampilkan View Invoice
        return view('kasir.orders.invoice', compact('order'));
    }
}
