

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Daftar Pesanan & Konfirmasi Pembayaran')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="<?php echo e(route('kasir.orders.index')); ?>">
                    <div class="flex space-x-2">
                        <input type="text" name="search" placeholder="Cari No. Pesanan atau Scan Kode..." 
                            class="flex-grow rounded-md border-gray-300 shadow-sm" 
                            value="<?php echo e($search); ?>">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md">Cari</button>
                        <?php if($search): ?>
                            <a href="<?php echo e(route('kasir.orders.index')); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="space-y-6">

                <?php if(session('success')): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"><?php echo e(session('error')); ?></div>
                <?php endif; ?>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2 text-yellow-700">
                        Pesanan Menunggu Pembayaran (Online/Transfer) (<?php echo e($pendingOrders->count()); ?>)
                    </h3>

                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">No. Pesanan</th>
                                <th class="px-6 py-3 text-left">Pelanggan</th> 
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-left">Tipe</th>
                                <th class="px-6 py-3 text-center">Aksi Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-yellow-50/50 transition duration-150">
                                <td class="px-6 py-4 font-medium">#<?php echo e($order->nomor_pesanan); ?></td>
                                <td class="px-6 py-4"><?php echo e($order->user->name ?? 'Pelanggan Online'); ?></td>
                                <td class="px-6 py-4 text-right font-semibold">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></td>
                                <td class="px-6 py-4"><?php echo e(ucfirst(str_replace('_', ' ', $order->tipe_pemesanan))); ?></td>
                                
                                <td class="px-6 py-4 text-center">
                                    
                                    <form action="<?php echo e(route('kasir.orders.process', $order)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin pesanan ini sudah dibayar dan siap diproses?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs shadow-md">
                                            ACC & Proses
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pesanan menunggu pembayaran saat ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    

                </div>
            </div>

                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2 text-indigo-700">
                            Pesanan Sedang Diproses (<?php echo e($activeOrders->count()); ?>)
                        </h3>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">No. Pesanan</th>
                                    <th class="px-6 py-3 text-left">Pelanggan</th> <th class="px-6 py-3 text-left">Total</th>
                                    <th class="px-6 py-3 text-left">Metode Bayar</th>
                                    <th class="px-6 py-3 text-left">Status Pesanan</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $activeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-6 py-4 font-medium">#<?php echo e($order->nomor_pesanan); ?></td>
                                        <td class="px-6 py-4"><?php echo e($order->user->name ?? 'Kasir Input'); ?></td> 
                                        <td class="px-6 py-4">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></td>
                                        <td class="px-6 py-4 text-sm"><?php echo e($order->payment_method_final ?? 'Belum Lunas'); ?></td> 
                                        <td class="px-6 py-4 text-sm"><?php echo e($order->status_pesanan); ?></td>
                                        <td class="px-6 py-4 text-center">
                                            
                                            <form action="<?php echo e(route('kasir.orders.complete', $order)); ?>" method="POST" onsubmit="return confirm('Selesaikan pesanan #<?php echo e($order->nomor_pesanan); ?>? Stok akan dikurangi.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Selesaikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <hr class="my-8">

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pesanan Selesai (<?php echo e($completedOrders->count()); ?>)</h2>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">No. Pesanan</th>
                                    <th class="px-6 py-3 text-left">Pelanggan</th>
                                    <th class="px-6 py-3 text-left">Total</th>
                                    <th class="px-6 py-3 text-left">Selesai Pada</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $completedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 font-medium">#<?php echo e($order->nomor_pesanan); ?></td>
                                    <td class="px-6 py-4"><?php echo e($order->user->name ?? 'Kasir Input'); ?></td>
                                    <td class="px-6 py-4">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></td>
                                    <td class="px-6 py-4 text-sm"><?php echo e($order->updated_at->format('d/m H:i')); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        
                                        <form action="<?php echo e(route('kasir.orders.revert', $order)); ?>" method="POST" onsubmit="return confirm('Yakin ingin membatalkan status Selesai dan memproses kembali pesanan #<?php echo e($order->nomor_pesanan); ?>?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Pindahkan Kembali
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pesanan yang selesai.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/kasir/orders/index.blade.php ENDPATH**/ ?>