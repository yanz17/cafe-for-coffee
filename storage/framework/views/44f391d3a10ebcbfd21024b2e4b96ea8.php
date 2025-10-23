

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Kelola Inventaris Bahan Baku')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    
                    <div class="flex justify-between items-center mb-4">
                        <a href="<?php echo e(route('manager.dashboard')); ?>" class="text-sm text-red-600 hover:text-red-800 font-semibold">
                            Lihat Laporan Stok Kritis &rarr;
                        </a>
                        <a href="<?php echo e(route('manager.bahan_baku.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Bahan Baku Baru
                        </a>
                    </div>

                    
                    <?php if(session('success')): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Minimal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $bahanBakus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bahanBaku): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($bahanBaku->stok_saat_ini <= $bahanBaku->stok_minimal ? 'bg-red-50' : ''); ?>">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900"><?php echo e($bahanBaku->nama); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php echo e(number_format($bahanBaku->stok_saat_ini, 0, ',', '.')); ?> <?php echo e($bahanBaku->unit); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php echo e(number_format($bahanBaku->stok_minimal, 0, ',', '.')); ?> <?php echo e($bahanBaku->unit); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($bahanBaku->stok_saat_ini <= $bahanBaku->stok_minimal): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            KRITIS
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aman
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <a href="<?php echo e(route('manager.bahan_baku.edit', $bahanBaku)); ?>" class="text-indigo-600 hover:text-indigo-900">Edit / Restock</a>
                                    
                                    
                                    <form action="<?php echo e(route('manager.bahan_baku.destroy', $bahanBaku)); ?>" method="POST" class="inline ml-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini? Tindakan ini tidak dapat dibatalkan.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada bahan baku yang terdaftar.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/bahan_baku/index.blade.php ENDPATH**/ ?>