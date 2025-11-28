

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Kelola Kategori Menu')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <?php if(session('success')): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('info')): ?>
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4"><?php echo e(session('info')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        Periksa kembali input Anda.
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    
                    <div class="border p-4 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold mb-4">Tambah Baru</h3>
                        <form action="<?php echo e(route('manager.categories.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="new_category_name" placeholder="Nama Kategori Baru" 
                                   class="w-full rounded-md border-gray-300 shadow-sm mb-3 <?php $__errorArgs = ['new_category_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   required>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded">
                                Simpan Kategori
                            </button>
                        </form>
                    </div>

                    
                    <div class="border p-4 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold mb-4">Kategori Yang Ada</h3>
                        <ul class="space-y-3">
                            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                <span class="font-medium"><?php echo e($category); ?></span>
                                
                                <div x-data="{ openEdit: false }">
                                    
                                    <button @click="openEdit = true" class="text-blue-500 hover:text-blue-700 text-sm mr-2">Edit</button>

                                    
                                    <form action="<?php echo e(route('manager.categories.destroy')); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin menghapus kategori <?php echo e($category); ?>? Menu akan disetel ke Uncategorized.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <input type="hidden" name="category_name" value="<?php echo e($category); ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                    </form>

                                    
                                    <div x-show="openEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                        <div @click.away="openEdit = false" class="bg-white p-6 rounded-lg w-96">
                                            <h4 class="text-lg font-bold mb-3">Edit Kategori: <?php echo e($category); ?></h4>
                                            <form action="<?php echo e(route('manager.categories.update')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <input type="hidden" name="old_category_name" value="<?php echo e($category); ?>">
                                                <input type="text" name="new_category_name" value="<?php echo e($category); ?>" class="w-full rounded-md border-gray-300 shadow-sm mb-3" required>
                                                <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Simpan</button>
                                                <button type="button" @click="openEdit = false" class="ml-2 bg-gray-300 py-2 px-4 rounded">Batal</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-gray-500">Belum ada kategori yang terdeteksi.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/menus/categories/index.blade.php ENDPATH**/ ?>