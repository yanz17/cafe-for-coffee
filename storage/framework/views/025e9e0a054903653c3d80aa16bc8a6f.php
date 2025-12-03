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
            
            
            <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow-inner border-t">
                <h3 class="text-2xl font-bold mb-4">Umpan Balik Pelayanan</h3>

                <?php
                    $feedback = \App\Models\Feedback::where('order_id', $order->id)->first();
                ?>

                <?php if($order->status_pesanan !== 'selesai'): ?>
                    <p class="text-gray-500">Anda dapat memberikan umpan balik setelah pesanan berstatus **Selesai**.</p>
                <?php elseif($feedback): ?>
                    
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-green-600 font-bold mb-2">Terima kasih! Umpan balik sudah terkirim.</p>
                        
                        
                        <div class="flex items-center space-x-1 mb-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <svg class="h-8 w-8 transition duration-150 ease-in-out fill-current 
                                    <?php if($feedback->rating >= $i): ?> text-yellow-500 <?php else: ?> text-gray-300 <?php endif; ?>" 
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            <?php endfor; ?>
                            <span class="text-sm font-semibold ml-2 text-gray-700">(<?php echo e($feedback->rating); ?> Bintang)</span>
                        </div>

                        
                        <?php if($feedback->tags && is_array($feedback->tags)): ?>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="text-xs font-semibold text-gray-700">Tags:</span>
                            <?php $__currentLoopData = $feedback->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php
                                    $isGood = in_array($tag, ['Enak', 'Cepat', 'Bersih', 'Ramah', 'Nyaman', 'Suka Kopinya']);
                                ?>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium shadow-sm 
                                             <?php echo e($isGood ? 'bg-indigo-100 text-indigo-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo e($tag); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                        
                        <p class="font-semibold text-gray-700 mt-3">Komentar: "<?php echo e($feedback->komentar ?? '-'); ?>"</p>
                    </div>
                <?php else: ?>
                    
                    <div x-data="{ 
                        rating: 5, 
                        hoverRating: 0, 
                        selectedTags: [],
                        goodTags: ['Enak', 'Cepat', 'Bersih', 'Ramah', 'Nyaman', 'Suka Kopinya'], // KELOMPOK BAIK
                        badTags: ['Lama', 'Kotor', 'Mahal', 'Pahit', 'Biasa Saja'], // KELOMPOK BURUK
                    }">
                        <p class="text-lg font-semibold text-gray-700 mb-3 text-center">Seberapa Puaskah Anda?</p>
                        
                        
                        
                        <div class="flex items-center space-x-1 justify-center mb-4">
                            <template x-for="star in 5" :key="star">
                                <svg @click="rating = star"
                                     @mouseover="hoverRating = star"
                                     @mouseleave="hoverRating = 0"
                                     :class="{
                                         'text-yellow-500': star <= (hoverRating === 0 ? rating : hoverRating), 
                                         'text-gray-300': star > (hoverRating === 0 ? rating : hoverRating)
                                     }"
                                     class="h-12 w-12 cursor-pointer transition duration-150 ease-in-out fill-current transform hover:scale-105"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </template>
                        </div>
                        <p class="text-center mb-6 text-sm font-medium text-gray-500">
                            Anda memilih <span class="font-bold text-indigo-600" x-text="rating + ' Bintang'"></span>
                        </p>


                        
                        <div class="mb-6 space-y-4">
                            
                            
                            <div>
                                <label class="block text-sm font-semibold text-green-700 mb-2">👍 Apa yang Anda Suka?</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tag in goodTags" :key="tag">
                                        <button type="button" 
                                                @click="
                                                    const index = selectedTags.indexOf(tag);
                                                    if (index === -1) { selectedTags.push(tag); } 
                                                    else { selectedTags.splice(index, 1); }
                                                "
                                                :class="selectedTags.includes(tag) ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-indigo-100'"
                                                class="px-3 py-1 text-sm rounded-full transition duration-150"
                                                x-text="tag">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-semibold text-red-700 mb-2">👎 Apa yang Perlu Ditingkatkan?</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tag in badTags" :key="tag">
                                        <button type="button" 
                                                @click="
                                                    const index = selectedTags.indexOf(tag);
                                                    if (index === -1) { selectedTags.push(tag); } 
                                                    else { selectedTags.splice(index, 1); }
                                                "
                                                :class="selectedTags.includes(tag) ? 'bg-red-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-red-100'"
                                                class="px-3 py-1 text-sm rounded-full transition duration-150"
                                                x-text="tag">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        

                        <form action="<?php echo e(route('customer.order.feedback.store', $order)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            
                            
                            <input type="hidden" name="rating" :value="rating" required>
                            <input type="hidden" name="tags" :value="JSON.stringify(selectedTags)"> 
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Komentar Tambahan (Opsional)</label>
                                <textarea name="komentar" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Komentar Anda..."></textarea>
                                <?php $__errorArgs = ['komentar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded shadow-md">
                                Kirim Umpan Balik
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            
        </div>
        <div class="mt-4 text-center">
            <a href="<?php echo e(route('customer.orders')); ?>" class="text-gray-500 hover:text-gray-800">&larr; Kembali ke Riwayat Pesanan</a>
        </div>
    </div>

    <?php if($order->status_pembayaran == 'menunggu' && $order->snap_token): ?>
    
        
        <div class="mt-6 text-center">
            <button id="pay-button" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg">
                Lanjutkan Pembayaran
            </button>
            <p class="text-sm text-gray-500 mt-2">Jika pop-up tidak muncul otomatis, klik tombol di atas.</p>
        </div>

        
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo e(env('MIDTRANS_CLIENT_KEY')); ?>"></script>
        
        <script type="text/javascript">
            const snapToken = '<?php echo e($order->snap_token); ?>';

            document.addEventListener('DOMContentLoaded', function () {
                // Panggil pembayaran secara otomatis saat halaman dimuat
                if (snapToken) {
                    // Panggil pop-up Midtrans
                    window.snap.pay(snapToken, {
                        onSuccess: function(result){
                            alert("Pembayaran Berhasil!");
                            window.location.reload(); 
                        },
                        onPending: function(result){
                            alert("Pembayaran Anda sedang diproses.");
                            window.location.reload(); 
                        },
                        onClose: function(){
                            // Opsional: Jika user menutup pop-up tanpa menyelesaikan pembayaran
                            console.log('Pembayaran ditutup.');
                        }
                    });
                }
            });
            
            // Listener untuk tombol manual
            document.getElementById('pay-button').onclick = function () {
                window.snap.pay(snapToken);
            };
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/customer/orders/show.blade.php ENDPATH**/ ?>