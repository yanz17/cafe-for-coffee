

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Detail Pesanan #') . $order->nomor_pesanan); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                
                <div class="mb-6 p-4 rounded-lg 
                    <?php if($order->status_pembayaran == 'lunas'): ?> bg-green-100 border-green-400 text-green-700
                    <?php elseif($order->status_pembayaran == 'menunggu'): ?> bg-yellow-100 border-yellow-400 text-yellow-700
                    <?php else: ?> bg-gray-100 border-gray-400 text-gray-700
                    <?php endif; ?> border-l-4">
                    
                    <h4 class="text-lg font-bold">Status Pembayaran: <?php echo e(ucfirst($order->status_pembayaran)); ?></h4>
                    <p class="text-sm">Status Pesanan: <?php echo e(ucfirst(str_replace('_', ' ', $order->status_pesanan))); ?></p>

                    <?php if($order->status_pembayaran == 'menunggu'): ?>
                        <p class="mt-2 font-semibold">Instruksi: Silakan datang ke Kasir Cafe For Coffee dan sebutkan **Nomor Pesanan Anda (<?php echo e($order->nomor_pesanan); ?>)** untuk menyelesaikan pembayaran.</p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <h5 class="font-bold text-gray-700">Tipe Pesanan</h5>
                        <p class="capitalize"><?php echo e(str_replace('_', ' ', $order->tipe_pemesanan)); ?> 
                            <?php if($order->meja): ?> (Meja No. <?php echo e($order->meja); ?>) <?php endif; ?></p>
                    </div>
                    <div>
                        <h5 class="font-bold text-gray-700">Waktu Pesan</h5>
                        <p><?php echo e($order->created_at->format('d F Y, H:i')); ?></p>
                    </div>
                </div>

                
                <h3 class="text-xl font-bold border-t pt-4 mt-4 mb-3">Item Pesanan</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Menu</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo e($item->menu->nama); ?></td>
                            <td class="px-6 py-4 text-center"><?php echo e($item->kuantitas); ?></td>
                            <td class="px-6 py-4 text-right">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                
                <div class="flex justify-end mt-4">
                    <div class="w-1/2 md:w-1/3">
                        <div class="flex justify-between font-bold text-xl border-t pt-2">
                            <span>TOTAL AKHIR:</span>
                            <span class="text-indigo-600">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="<?php echo e(route('customer.orders')); ?>" class="text-gray-500 hover:text-gray-800">&larr; Kembali ke Riwayat Pesanan</a>
            </div>
        </div>
    </div>
    <script>
        <?php if(session('clear_cart')): ?>
            localStorage.removeItem('cafe_for_coffee_cart');
        <?php endif; ?>
    </script>

    
    <?php
        //$feedback = $order->feedback; // Anda perlu menambahkan relasi feedback ke model Order
    ?>

    
    <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow-inner">
        <h3 class="text-xl font-bold mb-4">Berikan Umpan Balik</h3>

        <?php if($order->status_pesanan !== 'selesai'): ?>
            <p class="text-gray-500">Umpan balik dapat diberikan setelah pesanan berstatus Selesai.</p>
        <?php elseif($feedback): ?>
            <p class="text-green-600 font-semibold">Anda sudah memberikan rating <?php echo e($feedback->rating); ?> bintang. Terima kasih!</p>
            <p class="text-gray-700 mt-2">Komentar: "<?php echo e($feedback->komentar ?? '-'); ?>"</p>
        <?php else: ?>
            
            <form action="<?php echo e(route('customer.order.feedback.store', $order)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Rating (1-5)</label>
                    <input type="number" name="rating" min="1" max="5" required class="mt-1 block w-20 rounded-md border-gray-300 shadow-sm" value="<?php echo e(old('rating')); ?>">
                    <?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Komentar (Opsional)</label>
                    <textarea name="komentar" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><?php echo e(old('komentar')); ?></textarea>
                    <?php $__errorArgs = ['komentar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Kirim Umpan Balik
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/customer/orders/show.blade.php ENDPATH**/ ?>