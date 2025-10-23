

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight"><?php echo e(__('Riwayat Pesananku')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 
                    <?php echo e($order->status_pembayaran == 'lunas' && $order->status_pesanan == 'selesai' ? 'border-green-600' : 
                       ($order->status_pembayaran == 'menunggu' ? 'border-yellow-600' : 'border-indigo-600')); ?>">
                    
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-lg font-bold">#<?php echo e($order->nomor_pesanan); ?></span>
                        <span class="text-sm text-gray-500"><?php echo e($order->created_at->format('d M Y H:i')); ?></span>
                    </div>

                    <p class="text-gray-700">Total: <span class="font-semibold text-lg text-indigo-600">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></span></p>

                    <div class="mt-3 flex items-center space-x-3 text-sm">
                        <span class="font-medium">Status Pembayaran:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            <?php echo e($order->status_pembayaran == 'lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                            <?php echo e(ucfirst($order->status_pembayaran)); ?>

                        </span>
                        
                        <span class="font-medium">Status Pesanan:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-800">
                            <?php echo e(ucfirst(str_replace('_', ' ', $order->status_pesanan))); ?>

                        </span>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="<?php echo e(route('customer.orders.show', $order)); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">Lihat Detail & Instruksi &rarr;</a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Anda belum memiliki riwayat pesanan.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/customer/orders/index.blade.php ENDPATH**/ ?>