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
                    <div wire:click="addToCart(<?php echo e($menu->id); ?>)"
                        class="border p-3 rounded-lg cursor-pointer transition duration-150 
                                <?php if($menu->is_tersedia): ?> hover:bg-indigo-50 <?php else: ?> bg-gray-100 opacity-60 cursor-not-allowed <?php endif; ?>"
                        <?php if(!$menu->is_tersedia): ?> title="Menu tidak tersedia" <?php endif; ?>>
                        <p class="font-bold text-gray-800"><?php echo e($menu->nama); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($menu->kategori); ?></p>
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
                        <div class="flex items-center space-x-2 w-1/2 justify-end">
                            
                            <input type="number" 
                                   wire:model.live.blur="cart.<?php echo e($index); ?>.kuantitas"
                                   min="1" 
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
</div><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/livewire/kasir-pos.blade.php ENDPATH**/ ?>