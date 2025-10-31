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
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


// Jika user BELUM login, arahkan ke halaman login
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

Route::get('serve-photo/{filename}', function ($filename) {
    $filePath = 'menu_photos/' . $filename;
    
    if (Storage::disk('public')->exists($filePath)) {
        
        // KUNCI PERBAIKAN: Gunakan response()->file() yang lebih eksplisit
        return response()->file(Storage::disk('public')->path($filePath));
        
    }
    abort(404);
})->where('filename', '.*')->name('serve.photo');

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
Route::prefix('manager')->middleware(['auth', 'role:'. User::ROLE_MANAGER])->group(function () {
    
    // 1. DASHBOARD MANAJER
    // URL: /manager/dashboard
    Route::get('/dashboard', [ReportController::class, 'dashboardSummary'])
        ->name('manager.dashboard');

    // 2. KELOLA USER
    // URL: /manager/users
    Route::resource('users', UserController::class)->names('manager.users');
    
    // 3. KELOLA MENU & RESEP
    // URL: /manager/menus
    Route::resource('menus', MenuController::class)->names('manager.menus');
    Route::get('menus/{menu}/resep', [MenuController::class, 'showRecipeForm'])->name('manager.menus.recipe.show');
    Route::post('menus/{menu}/resep', [MenuController::class, 'storeRecipe'])->name('manager.menus.recipe.store');
    
    // 4. KELOLA BAHAN BAKU
    // URL: /manager/bahan_baku
    Route::resource('bahan_baku', BahanBakuController::class)->names('manager.bahan_baku');
    
    // 5. KELOLA KATEGORI (FIX 404)
    // URL: /manager/categories
    Route::prefix('categories')->name('manager.categories.')->group(function () {
        Route::get('/', [MenuController::class, 'categoriesIndex'])->name('index');
        Route::post('/', [MenuController::class, 'categoryStore'])->name('store');
        Route::put('/', [MenuController::class, 'categoryUpdate'])->name('update');
        Route::delete('/', [MenuController::class, 'categoryDestroy'])->name('destroy');
    });

    // 6. LAPORAN
    // URL: /manager/reports/...
    Route::prefix('reports')->name('manager.reports.')->group(function () {
        Route::get('sales', [ReportController::class, 'salesReport'])->name('sales');
        Route::get('inventory-status', [ReportController::class, 'inventoryStatus'])->name('inventory_status');
        Route::get('popularity', [ReportController::class, 'productPopularity'])->name('popularity');
        Route::get('charts', [ReportController::class, 'salesChartData'])->name('charts');
        Route::get('customers', [ReportController::class, 'customerSegmentation'])->name('customers');
        Route::get('recommendations', [ReportController::class, 'productRecommendations'])->name('recommendations');
    });
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
