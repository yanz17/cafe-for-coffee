<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Menu Cafe For Coffee')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-6" x-data="customerOrderData()" x-init="init()"> 
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            
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

            
            <?php if(auth()->check()): ?>
                <div class="mb-8 p-4 rounded-xl <?php echo e($recommendations->isNotEmpty() ? 'bg-yellow-50 border-l-4 border-yellow-500 shadow-lg' : 'bg-gray-100 shadow-md'); ?>">
                    <h3 class="text-xl font-bold <?php echo e($recommendations->isNotEmpty() ? 'text-yellow-800' : 'text-gray-700'); ?> mb-3">
                        Rekomendasi Spesial Untuk Anda
                    </h3>
                    
                    <?php if($recommendations->isNotEmpty()): ?>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php $__currentLoopData = $recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition cursor-pointer" 
                                @click="addItem(<?php echo e($menu->id); ?>, '<?php echo e($menu->nama); ?>', <?php echo e($menu->harga); ?>)">
                                <?php if($menu->foto): ?>
                                    <img src="<?php echo e(url('serve-photo/' . basename($menu->foto))); ?>" alt="<?php echo e($menu->nama); ?>" 
                                         class="h-20 w-full object-cover rounded-md mb-1">
                                <?php endif; ?>
                                <p class="font-semibold text-sm"><?php echo e($menu->nama); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($menu->kategori); ?></p>
                                <p class="text-sm font-semibold text-red-600">Rp <?php echo e(number_format($menu->harga, 0, ',', '.')); ?></p>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Lakukan transaksi pertama Anda untuk mendapatkan rekomendasi terbaik!</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            

            
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <input type="text" x-model="searchTerm" placeholder="Cari nama menu..." 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
            </div>

            
            <div class="space-y-10">
                <?php $__empty_1 = true; $__currentLoopData = $groupedMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $menus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-white shadow-xl sm:rounded-lg p-4">
                        <h3 class="text-2xl font-bold mb-4 border-b pb-2 text-indigo-700"><?php echo e($kategori); ?></h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $safeName = json_encode($menu->nama);
                                    $maxStok = $menu->max_stok;
                                    $isAvailable = $maxStok > 0;
                                ?>

                                
                                <div x-show="isMenuVisible('<?php echo e($safeName); ?>', searchTerm)" 
                                     @click="<?php echo e($isAvailable ? 'addItem('. $menu->id .', ' . $safeName . ', '. $menu->harga .', '. $maxStok .')' : 'alert(\'Mohon maaf, stok sudah habis.\')'); ?>"
                                     class="border p-4 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 flex justify-between items-center space-x-3">
                                    
                                    <div class="flex items-center space-x-3 flex-grow">
                                        <?php if($menu->foto): ?>
                                            <img src="<?php echo e(url('serve-photo/' . basename($menu->foto))); ?>" alt="<?php echo e($menu->nama); ?>" 
                                                 class="h-20 w-20 object-cover rounded-md flex-shrink-0">
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-bold text-lg text-gray-800"><?php echo e($menu->nama); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo e($menu->kategori); ?></p>
                                            <p class="text-xs font-semibold <?php echo e($isAvailable ? 'text-green-600' : 'text-red-600'); ?>">
                                                Stok: <?php echo e(number_format($maxStok)); ?> pcs tersedia
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-xl font-semibold text-indigo-600 flex-shrink-0">Rp <?php echo e(number_format($menu->harga, 0, ',', '.')); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 pt-5">Saat ini tidak ada menu yang tersedia.</p>
                <?php endif; ?>
            </div>

            
            <div x-show="cart.length > 0" x-transition:enter class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-2xl p-4 z-50">
                <div class="max-w-4xl mx-auto flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-lg font-semibold" x-text="cart.length + ' Item'"></span>
                        <span class="text-2xl font-bold text-green-600" x-text="formatRupiah(calculateTotal())"></span>
                    </div>
                    
                    <button @click="$dispatch('open-checkout')" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition duration-150">
                        Checkout
                    </button>
                </div>
            </div>
        </div>

        
        <div x-data="{ open: false }" @open-checkout.window="open = true" x-show="open" 
             class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
            <div @click.away="open = false" class="relative top-20 mx-auto p-5 border w-full md:w-1/3 shadow-lg rounded-md bg-white">
                <h3 class="text-2xl font-bold border-b pb-2 mb-4">Konfirmasi Pesanan</h3>
                
                <form action="<?php echo e(route('customer.order.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    
                    <div class="max-h-60 overflow-y-auto space-y-3 mb-4">
                        <template x-for="(item, index) in cart" :key="item.menu_id">
                            <div class="flex justify-between items-center border-b py-2">
                                <p class="text-sm w-1/2" x-text="item.nama"></p>
                                <input type="number" x-model.number="item.kuantitas" @input="updateSubtotal(index)" 
                                    min="1" 
                                    :max="item.stok"
                                    class="w-16 border-gray-300 rounded-md text-center p-1">
                                <p class="font-semibold text-sm w-1/4 text-right" x-text="formatRupiah(item.subtotal)"></p>
                                <button @click="removeItem(index)" type="button" class="text-red-500 hover:text-red-700 ml-2">×</button>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-between font-bold text-xl border-t pt-2 mb-4">
                        <span>Total Akhir:</span>
                        <span x-text="formatRupiah(calculateTotal())"></span>
                    </div>

                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tipe Pesanan</label>
                        <select name="tipe_pemesanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" x-model="orderType">
                            <option value="take_away">Take Away</option>
                            <option value="dine_in">Dine In</option>
                        </select>
                    </div>
                    
                    <div class="mb-4" x-show="orderType === 'dine_in'">
                        <label class="block text-sm font-medium text-gray-700">Nomor Meja</label>
                        <input type="text" name="meja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Metode Pembayaran (Bayar di Kasir)</label>
                        <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="Cash">Tunai (Bayar Saat Ambil)</option>
                        </select>
                    </div>

                    
                    <input type="hidden" name="items" :value="JSON.stringify(cart.map(item => ({ 
                        menu_id: item.menu_id, 
                        kuantitas: item.kuantitas,
                    })))">

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded">
                        Buat Pesanan & Bayar di Kasir
                    </button>
                </form>

            </div>
        </div>
    </div>

    
    <script>
        // Definisikan fungsi data utama secara global untuk x-data
        window.customerOrderData = function() {
            return {
                cart: [],
                orderType: 'take_away',
                searchTerm: '', // State pencarian

                init() {
                    // Load cart dari localStorage
                    let savedCart = localStorage.getItem('cafe_for_coffee_cart');
                    if (savedCart) {
                        this.cart = JSON.parse(savedCart);
                    }
                    // Watch for changes and save
                    this.$watch('cart', () => {
                        localStorage.setItem('cafe_for_coffee_cart', JSON.stringify(this.cart));
                    });
                },

                updateSubtotal(index) {
                    let item = this.cart[index];
                    if (item.kuantitas > item.stok) {
                        item.kuantitas = item.stok; // Batasi ke stok maksimum
                        // Opsional: tampilkan notifikasi jika ini terjadi
                        alert(`Kuantitas untuk ${item.nama} telah dibatasi sesuai stok: ${item.stok}`);
                    }
                    if (item.kuantitas < 1) {
                        item.kuantitas = 1; // Batasi ke minimum 1
                    }

                    item.subtotal = item.kuantitas * item.harga_satuan;
                    this.cart = [...this.cart];
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.cart = [...this.cart]; 
                },
                
                addItem(menu_id, nama, harga, stok) {
                    if (stok <= 0) {
                        alert(`Maaf, stok ${nama} sudah habis.`);
                        return; // Jangan tambahkan jika stok 0
                    }

                    let existingItem = this.cart.find(item => item.menu_id === menu_id);

                    if (existingItem) {
                        // Cek Stok untuk penambahan
                        if (existingItem.kuantitas >= stok) {
                            alert(`Gagal menambahkan! Stok ${nama} tersisa hanya ${stok}.`);
                            return; // Jangan tambahkan jika sudah mencapai batas stok
                        }
                        existingItem.kuantitas++;
                        existingItem.subtotal = existingItem.kuantitas * existingItem.harga_satuan;
                    } else {
                        this.cart.push({
                            menu_id: menu_id,
                            nama: nama,
                            harga_satuan: harga,
                            kuantitas: 1,
                            subtotal: harga,
                            stok: stok, // TAMBAHKAN PROPERTI STOK
                        });
                    }
                    this.cart = [...this.cart];
                },

                calculateTotal() {
                    return this.cart.reduce((total, item) => total + item.subtotal, 0);
                },
                
                formatRupiah(angka) {
                    var number_string = angka.toString(),
                        sisa    = number_string.length % 3,
                        rupiah  = number_string.substr(0, sisa),
                        ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                            
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    return 'Rp ' + rupiah;
                },

                // LOGIC PENCARIAN (untuk x-show)
                isMenuVisible(menuName, term) {
                    if (!term || term.trim() === '') {
                        return true; 
                    }
                    // Menggunakan includes untuk pencarian non-case sensitive
                    return menuName.includes(term.toLowerCase().trim());
                },
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/customer/menu/index.blade.php ENDPATH**/ ?>