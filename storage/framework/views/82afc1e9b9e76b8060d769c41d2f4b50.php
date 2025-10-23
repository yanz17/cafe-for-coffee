

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Menu Cafe For Coffee')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-6" x-data="customerOrder()">
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
                                <p class="font-semibold text-sm"><?php echo e($menu->nama); ?></p>
                                <p class="text-xs text-red-600">Rp <?php echo e(number_format($menu->harga, 0, ',', '.')); ?></p>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Lakukan transaksi pertama Anda untuk mendapatkan rekomendasi terbaik!</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            

            
            <div class="bg-white shadow-xl sm:rounded-lg p-4 mb-8">
                <h3 class="text-2xl font-bold mb-4">Pilih Menu Favoritmu</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div @click="addItem(<?php echo e($menu->id); ?>, '<?php echo e($menu->nama); ?>', <?php echo e($menu->harga); ?>)"
                            class="border p-4 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-lg text-gray-800"><?php echo e($menu->nama); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($menu->kategori); ?></p>
                            </div>
                            <p class="text-xl font-semibold text-indigo-600">Rp <?php echo e(number_format($menu->harga, 0, ',', '.')); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
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
                                    min="1" class="w-16 border-gray-300 rounded-md text-center p-1">
                                <p class="font-semibold text-sm w-1/4 text-right" x-text="formatRupiah(item.subtotal)"></p>
                                <button @click="removeItem(index)" type="button" class="text-red-500 hover:text-red-700 ml-2">×</button>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-between font-bold text-xl border-t pt-2 mb-4">
                        <span>Total Akhir:</span>
                        <span x-text="formatRupiah(calculateTotal())"></span>
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
        function customerOrder() {
            return {
                cart: [],
                orderType: 'take_away',
                
                init() {
                    let savedCart = localStorage.getItem('cafe_for_coffee_cart');
                    if (savedCart) {
                        this.cart = JSON.parse(savedCart);
                    }
                    this.$watch('cart', () => {
                        localStorage.setItem('cafe_for_coffee_cart', JSON.stringify(this.cart));
                    });
                },

                updateSubtotal(index) {
                    this.cart[index].subtotal = this.cart[index].kuantitas * this.cart[index].harga_satuan;
                    this.cart = [...this.cart]; 
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.cart = [...this.cart]; 
                },
                
                addItem(menu_id, nama, harga) {
                    let existingItem = this.cart.find(item => item.menu_id === menu_id);

                    if (existingItem) {
                        existingItem.kuantitas++;
                        existingItem.subtotal = existingItem.kuantitas * existingItem.harga_satuan;
                    } else {
                        this.cart.push({
                            menu_id: menu_id,
                            nama: nama,
                            harga_satuan: harga,
                            kuantitas: 1,
                            subtotal: harga,
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
                }
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/customer/menu/index.blade.php ENDPATH**/ ?>