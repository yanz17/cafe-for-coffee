

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Laporan Rekomendasi Produk')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-4 border-b pb-2">Top 10 Produk yang Sering Dibeli Bersama</h3>
                <p class="text-gray-600 mb-6">Data ini menunjukkan pasangan menu yang paling sering muncul dalam satu transaksi (Lunas).</p>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Produk A (Jika Dibeli)</th>
                            <th class="px-6 py-3 text-left">Produk B (Rekomendasi)</th>
                            <th class="px-6 py-3 text-right">Frekuensi Pembelian Bersama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $topPairs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pair): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="px-6 py-4 font-medium"><?php echo e($menus[$pair['item_a_id']]->nama ?? 'Tidak Ditemukan'); ?></td>
                            <td class="px-6 py-4 font-medium"><?php echo e($menus[$pair['item_b_id']]->nama ?? 'Tidak Ditemukan'); ?></td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-700"><?php echo e(number_format($pair['count'])); ?> kali</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada pola pembelian yang signifikan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/recommendations-report.blade.php ENDPATH**/ ?>