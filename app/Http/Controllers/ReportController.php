<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\BahanBaku;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Menu;

class ReportController extends Controller
{
    /**
     * Menampilkan Laporan Penjualan berdasarkan rentang tanggal.
     */
    public function dashboardSummary()
    {
        // Variabel harus diinisialisasi untuk keamanan, meskipun query sum mengembalikan 0
        $todaySales = 0.0; 
        $criticalStockCount = 0;
        $recentOrders = collect();

        // Jalankan query tanpa try-catch (setelah yakin logic benar)
        $todaySales = (float) \App\Models\Order::where('status_pembayaran', 'lunas')
                        ->whereDate('created_at', \Carbon\Carbon::today()) 
                        ->sum('total_harga');

        $criticalStockCount = (int) \App\Models\BahanBaku::whereColumn('stok_saat_ini', '<=', 'stok_minimal')
                                ->count();
                                
        $recentOrders = \App\Models\Order::where('status_pembayaran', 'lunas')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        // Ini adalah satu-satunya sumber data untuk view
        return view('manager.reports.dashboard-summary', compact('todaySales', 'criticalStockCount', 'recentOrders'));
    }
    public function salesReport(Request $request)
    {
        // 1. Ambil input filter tanggal
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // 2. Query data pesanan yang sudah LUNAS
        $sales = Order::where('status_pembayaran', 'lunas')
                      ->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate)
                      ->orderBy('created_at', 'asc')
                      ->get();

        // 3. Hitung total summary
        $summary = [
            'total_penjualan' => $sales->sum('total_harga'),
            'total_order' => $sales->count(),
            'total_cash' => $sales->where('payment_method_final', 'Cash')->sum('total_harga'),
            'total_qris' => $sales->where('payment_method_final', 'QRIS')->sum('total_harga'),
            'total_transfer' => $sales->where('payment_method_final', 'Transfer')->sum('total_harga'),
        ];

        return view('manager.reports.sales-report', compact('sales', 'summary', 'startDate', 'endDate'));
    }
    
    /**
     * Menampilkan daftar bahan baku yang stoknya di bawah batas minimal (kritis).
     */
    public function inventoryStatus()
    {
        // Ambil bahan baku yang stok saat ini kurang dari atau sama dengan stok minimal
        $criticalStock = BahanBaku::whereColumn('stok_saat_ini', '<=', 'stok_minimal')
                                  ->orderBy('stok_saat_ini', 'asc')
                                  ->get();

        $allStock = BahanBaku::orderBy('nama', 'asc')->get();

        return view('manager.reports.inventory-report', compact('criticalStock', 'allStock'));
    }
    
    public function salesChartData()
    {
        // Periode 30 hari terakhir
        $endDate = now()->endOfDay();
        $startDate = now()->subDays(30)->startOfDay();

        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date'); // Kunci hasil dengan tanggal untuk pengisian hari kosong

        // Inisialisasi data untuk 30 hari penuh (membuat hari yang kosong jadi 0)
        $labels = [];
        $data = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->format('d M'); // Format untuk label chart
            
            $total = $salesData->get($dateString)['total'] ?? 0;
            $data[] = (int) $total;
            
            $currentDate->addDay();
        }

        return view('manager.reports.charts', compact('labels', 'data'));
    }

    public function customerSegmentation()
    {
        // Periode: 90 hari terakhir
        $startDate = Carbon::now()->subDays(90);

        // A. Top Spenders (Berdasarkan Total Uang yang Dihabiskan)
        $topSpenders = User::select(
            'users.id', 
            'users.name', 
            'users.email',
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('SUM(orders.total_harga) as total_spent')
        )
        ->join('orders', 'users.id', '=', 'orders.user_id')
        ->where('orders.status_pembayaran', 'lunas')
        ->where('orders.created_at', '>=', $startDate)
        ->groupBy('users.id', 'users.name', 'users.email')
        ->orderByDesc('total_spent')
        ->limit(10)
        ->get();


        // B. Frequent Buyers (Berdasarkan Jumlah Transaksi)
        $frequentBuyers = User::select(
            'users.id', 
            'users.name', 
            'users.email',
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('SUM(orders.total_harga) as total_spent')
        )
        ->join('orders', 'users.id', '=', 'orders.user_id')
        ->where('orders.status_pembayaran', 'lunas')
        ->where('orders.created_at', '>=', $startDate)
        ->groupBy('users.id', 'users.name', 'users.email')
        ->orderByDesc('total_orders')
        ->limit(10)
        ->get();

        return view('manager.reports.customer-segmentation', compact('topSpenders', 'frequentBuyers'));
    }

    public function productRecommendations()
    {
        // Mengambil semua Order ID yang sudah lunas (untuk analisis pola pembelian)
        $orderIds = Order::where('status_pembayaran', 'lunas')->pluck('id');

        // Mengambil semua item dalam pesanan yang sudah lunas
        $orderItems = OrderItem::whereIn('order_id', $orderIds)
            ->select('order_id', 'menu_id')
            ->get();

        $recommendations = [];

        // Algoritma Market Basket Analysis Sederhana
        // 1. Kelompokkan items berdasarkan Order ID
        $groupedOrders = $orderItems->groupBy('order_id');

        // 2. Iterasi setiap pesanan untuk menemukan pasangan item
        foreach ($groupedOrders as $itemsInOrder) {
            $menuIds = $itemsInOrder->pluck('menu_id')->unique()->values();
            
            // Cari semua pasangan item (i, j) dalam pesanan ini
            for ($i = 0; $i < count($menuIds); $i++) {
                for ($j = $i + 1; $j < count($menuIds); $j++) {
                    $itemA = $menuIds[$i];
                    $itemB = $menuIds[$j];

                    // Buat kunci unik untuk pasangan (e.g., '1-5' atau '5-1' harus sama)
                    $key = min($itemA, $itemB) . '-' . max($itemA, $itemB);

                    if (!isset($recommendations[$key])) {
                        $recommendations[$key] = [
                            'item_a_id' => min($itemA, $itemB),
                            'item_b_id' => max($itemA, $itemB),
                            'count' => 0,
                        ];
                    }
                    $recommendations[$key]['count']++;
                }
            }
        }

        // Ambil data Menu untuk pasangan teratas
        $topPairs = collect($recommendations)
            ->sortByDesc('count')
            ->take(10) // Ambil 10 pasangan teratas
            ->values()
            ->all();

        // Ambil nama menu untuk tampilan
        $menuIdsInPairs = collect($topPairs)->pluck('item_a_id')->merge(collect($topPairs)->pluck('item_b_id'))->unique();
        $menus = Menu::whereIn('id', $menuIdsInPairs)->get()->keyBy('id');

        return view('manager.reports.recommendations-report', compact('topPairs', 'menus'));
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


        return view('manager.reports.popularity-report', compact(
            'popularity', 
            'categorySummary',
            'startDate', 
            'endDate'
        ));
    }
}
