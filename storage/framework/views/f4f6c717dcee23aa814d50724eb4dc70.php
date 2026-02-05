 


<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight"><?php echo e(__('Semua Umpan Balik Pelanggan')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">Daftar Feedback (Total: <?php echo e($feedbacks->total()); ?>)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Kirim</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pesanan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($feedback->created_at->format('d M Y H:i')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        
                                        <span class="text-lg font-bold <?php echo e($feedback->rating >= 4 ? 'text-green-500' : 'text-yellow-500'); ?>">
                                            <?php for($i = 0; $i < $feedback->rating; $i++): ?>
                                                ★
                                            <?php endfor; ?>
                                        </span>
                                    </td>
                                    <td class="px-3 py-4">
                                        <?php if($feedback->balasan_manager): ?>
                                            <div class="bg-indigo-50 p-2 rounded border border-indigo-100">
                                                <span class="text-[10px] font-bold text-indigo-600 block mb-1 uppercase">Balasan Anda:</span>
                                                <p class="text-xs text-gray-700"><?php echo e($feedback->balasan_manager); ?></p>
                                            </div>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('manager.feedback.reply', $feedback)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <textarea name="balasan_manager" rows="2" class="w-full text-xs rounded border-gray-300" placeholder="Tulis balasan..."></textarea>
                                                <button type="submit" class="mt-1 bg-indigo-600 text-white text-[10px] px-2 py-1 rounded hover:bg-indigo-700">
                                                    Kirim Balasan
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800 max-w-xs overflow-hidden truncate">
                                        <?php echo e($feedback->komentar ?? 'Tidak ada komentar'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        <?php echo e($feedback->user->name ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #<?php echo e($feedback->order->nomor_pesanan ?? 'N/A'); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada umpan balik yang tersedia.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <?php echo e($feedbacks->links()); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/feedbacks/index.blade.php ENDPATH**/ ?>