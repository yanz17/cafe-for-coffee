

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Dashboard Manajer')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 relative group transform hover:scale-[1.02] transition duration-300 border-t-4 border-indigo-500">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 uppercase">Penjualan Hari Ini</p>
                            <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 18V6"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 15v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4"></path></svg>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 mt-3">
                            Rp <?php echo e(number_format($todaySales, 0, ',', '.')); ?>

                        </p>
                        <a href="<?php echo e(route('manager.reports.sales')); ?>" class="text-xs text-indigo-500 hover:text-indigo-700 mt-2 block">Lihat Detail Laporan &rarr;</a>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 relative group transform hover:scale-[1.02] transition duration-300 border-t-4 <?php echo e($criticalStockCount > 0 ? 'border-red-600' : 'border-green-600'); ?>">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500 uppercase">Bahan Baku Kritis</p>
                            <svg class="h-6 w-6 <?php echo e($criticalStockCount > 0 ? 'text-red-500' : 'text-green-500'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M10 17h.01"></path></svg>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 mt-3">
                            <?php echo e($criticalStockCount); ?> Jenis
                        </p>
                        <a href="<?php echo e(route('manager.reports.inventory_status')); ?>" class="text-xs <?php echo e($criticalStockCount > 0 ? 'text-red-600' : 'text-gray-500'); ?> mt-2 block font-semibold">
                            <?php if($criticalStockCount > 0): ?>
                                Perlu Restock Segera! &rarr;
                            <?php else: ?>
                                Stok Aman.
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 relative group transform hover:scale-[1.02] transition duration-300 border-t-4 border-yellow-500">
                         <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-500 uppercase">Pusat Laporan & Analisis</p>
                            <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0h2a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10"></path></svg>
                        </div>
                         
                         <ul class="space-y-1 mt-3">
                            <li class="font-semibold text-gray-800">Analisis Penjualan:</li>
                            <li><a href="<?php echo e(route('manager.reports.sales')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800">Laporan Keuangan Dasar (Penjualan)</a></li>
                            <li><a href="<?php echo e(route('manager.reports.charts')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800">Visualisasi & Tren Penjualan</a></li>
                            <li><a href="<?php echo e(route('manager.reports.popularity')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800">Popularitas Produk</a></li>
                            
                            <li class="font-semibold text-gray-800 pt-2">Business Insight:</li>
                            <li><a href="<?php echo e(route('manager.reports.customers')); ?>" class="text-sm text-green-600 hover:text-green-800">Segmentasi Pelanggan (Top Buyers)</a></li>
                            <li><a href="<?php echo e(route('manager.reports.recommendations')); ?>" class="text-sm text-green-600 hover:text-green-800">Rekomendasi Produk</a></li>
                            <li><a href="<?php echo e(route('manager.reports.inventory_status')); ?>" class="text-sm text-red-600 hover:text-red-800">Status Stok Kritis</a></li>
                         </ul>
                    </div>
                </div>

                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Transaksi Lunas Terbaru (Top 5)</h3>
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Nomor Pesanan</th>
                                    <th class="px-6 py-3 text-left">Waktu</th>
                                    <th class="px-6 py-3 text-left">Pelanggan</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3 text-center">Metode Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 font-medium"><?php echo e($order->nomor_pesanan); ?></td>
                                    <td class="px-6 py-4 text-sm"><?php echo e($order->created_at->diffForHumans()); ?></td>
                                    <td class="px-6 py-4 text-sm"><?php echo e($order->user->name ?? 'Kasir Langsung'); ?></td>
                                    <td class="px-6 py-4 text-right font-semibold">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($order->payment_method_final === 'Cash' ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                            <?php echo e($order->payment_method_final ?? 'POS'); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada transaksi yang tercatat.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/dashboard-summary.blade.php ENDPATH**/ ?>