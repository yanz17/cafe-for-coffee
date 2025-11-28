

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Laporan Segmentasi Pelanggan')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-10">

                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border-t-4 border-indigo-500">
                    <h3 class="text-2xl font-bold mb-4">Top 10 Pelanggan Terbaik (Total Pengeluaran 90 Hari)</h3>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Pelanggan</th>
                                <th class="px-6 py-3 text-right">Total Belanja (Rp)</th>
                                <th class="px-6 py-3 text-center">Jumlah Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $topSpenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-indigo-50/50 transition">
                                <td class="px-6 py-4 font-medium"><?php echo e($user->name); ?> (<?php echo e($user->email); ?>)</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-700">Rp <?php echo e(number_format($user->total_spent, 0, ',', '.')); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo e($user->total_orders); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada data pelanggan yang tersegmen.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border-t-4 border-green-500">
                    <h3 class="text-2xl font-bold mb-4">Top 10 Pembeli Paling Sering (90 Hari)</h3>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Pelanggan</th>
                                <th class="px-6 py-3 text-center">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right">Total Belanja (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $frequentBuyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-green-50/50 transition">
                                <td class="px-6 py-4 font-medium"><?php echo e($user->name); ?> (<?php echo e($user->email); ?>)</td>
                                <td class="px-6 py-4 text-center font-bold text-green-700"><?php echo e($user->total_orders); ?></td>
                                <td class="px-6 py-4 text-right">Rp <?php echo e(number_format($user->total_spent, 0, ',', '.')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada data pelanggan yang tersegmen.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/customer-segmentation.blade.php ENDPATH**/ ?>