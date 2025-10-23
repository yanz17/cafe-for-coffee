<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ReportController;


// Jika user BELUM login, arahkan ke halaman login
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

// Jika user SUDAH login, gunakan redirect berbasis role dari Controller Login
// Route root akan diarahkan ke route 'dashboard'
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});

// ROUTE FALLBACK DASHBOARD (Fix Loop: Ini akan memicu redirect role di Controller Login)
Route::get('/dashboard', function () {
    return view('dashboard-catchall'); // TANPA subfolder
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route untuk Manajer
Route::middleware(['auth', 'role:'. User::ROLE_MANAGER])->group(function () {
    
    // 1. DASHBOARD MANAJER
    Route::get('/manager/dashboard', [ReportController::class, 'dashboardSummary'])
        ->name('manager.dashboard');

    // 2. KELOLA USER (CRUD Kasir dan Manajer Lain)
    // Asumsi Anda menggunakan UserController untuk mengelola user di luar registrasi Breeze
    Route::resource('manager/users', UserController::class)->names('manager.users');
    
    // 3. KELOLA MENU & RESEP
    Route::resource('manager/menus', MenuController::class)->names('manager.menus');
    // Route untuk menampilkan dan menyimpan resep menu tertentu
    Route::get('manager/menus/{menu}/resep', [MenuController::class, 'showRecipeForm'])->name('manager.menus.recipe.show');
    Route::post('manager/menus/{menu}/resep', [MenuController::class, 'storeRecipe'])->name('manager.menus.recipe.store');
    
    // 4. KELOLA BAHAN BAKU (Inventaris)
    Route::resource('manager/bahan_baku', BahanBakuController::class)->names('manager.bahan_baku');
    
    // 5. LAPORAN
    Route::get('manager/reports/sales', [ReportController::class, 'salesReport'])->name('manager.reports.sales');
    Route::get('manager/reports/inventory-status', [ReportController::class, 'inventoryStatus'])->name('manager.reports.inventory_status');
    // ... Tambahkan route laporan lain (misalnya laporan HPP)

    // BARU: Laporan Popularitas Produk
    Route::get('manager/reports/popularity', [ReportController::class, 'productPopularity'])->name('manager.reports.popularity');
    // BARU: Halaman Chart Visualisasi
    Route::get('manager/reports/charts', [ReportController::class, 'salesChartData'])->name('manager.reports.charts');
    // BARU: Laporan Segmentasi Pelanggan
    Route::get('manager/reports/customers', [ReportController::class, 'customerSegmentation'])->name('manager.reports.customers');
    // BARU: Rekomendasi Produk
    Route::get('manager/reports/recommendations', [ReportController::class, 'productRecommendations'])->name('manager.reports.recommendations');
});

// Route untuk Kasir
Route::middleware(['auth', 'role:'. User::ROLE_KASIR])->group(function () {
    // 1. Antarmuka POS (Livewire)
    Route::get('/kasir/pos', \App\Livewire\KasirPos::class)->name('kasir.pos'); 

    // 2. Daftar Pesanan Aktif
    Route::get('/kasir/orders', [OrderController::class, 'index'])->name('kasir.orders.index');

    // 3. Konfirmasi Pembayaran
    // Kita perbaiki penamaannya menjadi konsisten: kasir.orders.confirm_payment
    Route::post('/kasir/order/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('kasir.orders.confirm_payment');
        
    // 4. Selesaikan Pesanan (complete)
    Route::put('/kasir/order/{order}/complete', [OrderController::class, 'completeOrder'])->name('kasir.orders.complete');

    // 5. Pindahkan Kembali (revert)
    Route::put('/kasir/order/{order}/revert', [OrderController::class, 'revertOrder'])->name('kasir.orders.revert');
    // BARU: Proses Pesanan (Menunggu Pembayaran -> Diproses/Aktif)
    Route::put('/kasir/order/{order}/process', [OrderController::class, 'processOrder'])->name('kasir.orders.process');
});

// Route untuk Pelanggan
Route::middleware(['auth', 'role:'. User::ROLE_PELANGGAN])->group(function () {
    // Pindahkan route CustomerController ke sini
    Route::get('/menu', [CustomerController::class, 'index'])->name('customer.menu');
    Route::post('/order', [CustomerController::class, 'storeOrder'])->name('customer.order.store');
    Route::get('/my-orders', [CustomerController::class, 'myOrders'])->name('customer.orders');
    Route::get('/my-orders/{order}', [CustomerController::class, 'showOrder'])->name('customer.orders.show');
    Route::post('/order/{order}/feedback', [CustomerController::class, 'storeFeedback'])->name('customer.order.feedback.store');
});

require __DIR__.'/auth.php';
