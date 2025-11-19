<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 flex space-x-4">
        
        
        <div class="w-1/2 bg-white shadow-xl sm:rounded-lg p-4 flex flex-col">
            <h3 class="text-xl font-semibold mb-3">Daftar Menu</h3>
            
            
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari menu..." 
                   class="w-full mb-4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300">

            
            <div class="grid grid-cols-3 gap-4 overflow-y-auto flex-grow h-[75vh]">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $maxStok = $menu->max_stok; // Akses Accessor
                        $isAvailable = $maxStok > 0;
                    ?>

                    <div wire:click="<?php echo e($isAvailable ? 'addToCart('.$menu->id.')' : ''); ?>"
                        class="border p-3 rounded-lg cursor-pointer transition duration-150 <?php echo e($isAvailable ? 'hover:bg-indigo-50' : 'opacity-60 cursor-not-allowed'); ?>">
                        <!--[if BLOCK]><![endif]--><?php if($menu->foto): ?>
                            <img src="<?php echo e(asset('storage/' . $menu->foto)); ?>" alt="<?php echo e($menu->nama); ?>" 
                                class="w-full h-24 object-cover rounded mb-2"> 
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <p class="font-bold text-gray-800"><?php echo e($menu->nama); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($menu->kategori); ?></p>
                        <p class="text-xs font-semibold <?php echo e($isAvailable ? 'text-green-600' : 'text-red-600'); ?>">
                            Stok: <?php echo e(number_format($maxStok)); ?> pcs tersedia
                        </p>
                        <p class="text-md font-semibold text-indigo-600">Rp <?php echo e(number_format($menu->harga, 0, ',', '.')); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 col-span-3">Menu tidak ditemukan.</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
        
        
        <div class="w-1/4 bg-white shadow-xl sm:rounded-lg p-4 flex flex-col">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Keranjang Pesanan</h3>

            <div class="space-y-3 overflow-y-auto flex-grow max-h-[70vh]">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center border-b pb-2">
                        <div class="w-1/2">
                            <p class="font-medium text-sm"><?php echo e($item['nama']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2 w-1/2 justify-end" wire:key="<?php echo e($item['menu_id']); ?>">
                            <input type="number" 
                                   wire:model.live.blur="cart.<?php echo e($index); ?>.kuantitas"
                                   value="<?php echo e($item['kuantitas']); ?>"
                                   min="1"
                                   max="<?php echo e($item['max_stok']); ?>"
                                   x-data="{ maxStok: <?php echo e($item['max_stok']); ?> }" 
                                    x-on:input="
                                        let val = parseInt($el.value);
                                        if (val > maxStok) {
                                            $el.value = maxStok; // Potong nilai di DOM
                                            $el.dispatchEvent(new Event('change')); // Memicu update Livewire secara eksplisit
                                            alert('Kuantitas maksimum ' + maxStok + ' sudah tercapai.');
                                        }
                                    "
                                   class="w-12 border-gray-300 rounded-md text-center p-1 text-sm">
                            <p class="font-semibold text-right w-16 text-sm">Rp <?php echo e(number_format($item['subtotal'], 0, ',', '.')); ?></p>
                            <button wire:click="removeItem(<?php echo e($index); ?>)" class="text-red-500 hover:text-red-700">×</button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 pt-5">Keranjang kosong.</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="mt-auto pt-4 border-t">
                <div class="flex justify-between font-bold text-xl mb-4">
                    <span>TOTAL:</span>
                    <span>Rp <?php echo e(number_format($this->total, 0, ',', '.')); ?></span>
                </div>
            </div>
        </div>
        
        
        <div class="w-1/4 bg-white shadow-xl sm:rounded-lg p-4">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Pembayaran</h3>

            <!--[if BLOCK]><![endif]--><?php if(session('success')): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if(session('error')): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            
            <form wire:submit.prevent="storeOrder">
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Tipe Pesanan</label>
                    <select wire:model.live="orderType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="dine_in">Dine In</option>
                        <option value="take_away">Take Away</option>
                    </select>
                </div>

                
                <!--[if BLOCK]><![endif]--><?php if($orderType === 'dine_in'): ?>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nomor Meja</label>
                        <input type="text" wire:model="meja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Metode Bayar</label>
                    <select wire:model="paymentMethod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="Cash">Cash (Tunai)</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer Bank</option>
                    </select>
                </div>
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Uang Diterima (Rp)</label>
                    <input type="number" 
                           wire:model.live.debounce.300ms="amountPaid" 
                           min="<?php echo e($this->total); ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['amountPaid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                
                
                <div class="flex justify-between font-bold text-lg mb-6 pt-2 border-t">
                    <span>Kembalian:</span>
                    <span class="<?php echo e($changeDue < 0 ? 'text-red-600' : 'text-green-600'); ?>">
                        Rp <?php echo e(number_format($changeDue, 0, ',', '.')); ?>

                    </span>
                </div>

                <button type="submit" 
                    
                    <?php if(empty($cart) || $changeDue < 0): ?> disabled <?php endif; ?>
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded disabled:opacity-50 transition duration-150">
                    Proses & Cetak Struk
                </button>
            </form>
        </div>
        
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-invoice-tab', (orderId) => { // <--- Langsung terima ID sebagai parameter
            
            // Kunci Perbaikan 1: Ambil ID dari array jika diperlukan, atau langsung dari parameter
            let id;
            if (Array.isArray(orderId) && orderId.length > 0) {
                 id = orderId[0];
            } else {
                 id = orderId;
            }
            
            // Kunci Perbaikan 2: Pastikan itu adalah integer yang valid
            const finalId = parseInt(id);

            if (finalId > 0 && !isNaN(finalId)) {
                const invoiceUrl = `/kasir/order/${finalId}/invoice`;
                
                // Membuka URL Invoice di tab baru
                window.open(invoiceUrl, '_blank'); 
            } else {
                console.error('FINAL ERROR: Gagal mengambil Order ID. Payload:', orderId);
                alert('Transaksi berhasil, tetapi gagal mencetak struk (ID tidak valid).');
            }
        });
    });
</script><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/livewire/kasir-pos.blade.php ENDPATH**/ ?>