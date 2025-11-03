

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Laporan Keuangan Dasar (Penjualan)')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-4">Laporan Penjualan (Periode: <?php echo e($startDate); ?> s/d <?php echo e($endDate); ?>)</h3>

                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg">
                        <p class="text-sm text-indigo-700">Total Pendapatan</p>
                        <p class="text-2xl font-bold">Rp <?php echo e(number_format($summary['total_penjualan'], 0, ',', '.')); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 border-l-4 border-gray-500 rounded-lg">
                        <p class="text-sm text-gray-700">Total Transaksi</p>
                        <p class="text-2xl font-bold"><?php echo e($summary['total_order']); ?></p>
                    </div>
                    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <p class="text-sm text-green-700">Total Cash</p>
                        <p class="text-xl font-bold">Rp <?php echo e(number_format($summary['total_cash'], 0, ',', '.')); ?></p>
                    </div>
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                        <p class="text-sm text-blue-700">Total Digital</p>
                        <p class="text-xl font-bold">Rp <?php echo e(number_format($summary['total_qris'] + $summary['total_transfer'], 0, ',', '.')); ?></p>
                    </div>
                </div>

                
                <h3 class="text-xl font-bold mb-3 border-t pt-4">Detail Transaksi</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">No. Pesanan</th>
                            <th class="px-6 py-3 text-left">Waktu</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-center">Metode Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 font-medium"><?php echo e($order->nomor_pesanan); ?></td>
                            <td class="px-6 py-4 text-sm"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></td>
                            <td class="px-6 py-4 text-right font-semibold">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></td>
                            <td class="px-6 py-4 text-center"><?php echo e($order->payment_method_final ?? 'Cash'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada transaksi lunas dalam periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/sales-report.blade.php ENDPATH**/ ?>