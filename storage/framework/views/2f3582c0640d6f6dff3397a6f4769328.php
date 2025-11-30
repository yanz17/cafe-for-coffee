<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Laporan Penjualan OLAP & Visualisasi')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-6">Analisis Penjualan Dinamis</h3>

                
                <form method="GET" action="<?php echo e(route('manager.reports.sales')); ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end mb-8 p-4 border rounded-lg bg-gray-50">
                    
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai</label>
                        <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="mt-1 block w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai</label>
                        <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="mt-1 block w-full rounded-lg border-gray-300">
                    </div>

                    
                    <div>
                        <label for="group_by" class="block text-sm font-medium text-gray-700">Kelompokkan Berdasarkan (Roll-up)</label>
                        <select name="group_by" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="date" <?php echo e($groupBy == 'date' ? 'selected' : ''); ?>>Tanggal Harian</option>
                            <option value="category" <?php echo e($groupBy == 'category' ? 'selected' : ''); ?>>Kategori Menu</option>
                            <option value="method" <?php echo e($groupBy == 'method' ? 'selected' : ''); ?>>Metode Pembayaran</option>
                            <option value="menu" <?php echo e($groupBy == 'menu' ? 'selected' : ''); ?>>Nama Menu</option> 
                        </select>
                    </div>

                    
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Filter Metode Bayar</label>
                        <select name="payment_method" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">-- Semua Metode --</option>
                            <?php $__currentLoopData = ['Cash', 'QRIS', 'Transfer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($method); ?>" <?php echo e($paymentMethod == $method ? 'selected' : ''); ?>><?php echo e($method); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <button type="submit" class="w-full md:w-auto self-end bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                        Tampilkan Data
                    </button>
                </form>

                <hr class="mb-8">

                
                <?php if($groupBy === 'menu'): ?>
                    <h3 class="text-xl font-bold mb-4">📈 Ringkasan Popularitas Menu (<?php echo e($startDate); ?> s/d <?php echo e($endDate); ?>)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                        
                        
                        <div class="border-2 border-green-200 p-4 rounded-xl bg-green-50 shadow-md">
                            <h4 class="text-lg font-semibold text-green-700 mb-3 flex items-center">
                                Menu Paling Laris (Top 5)
                            </h4>
                            <ol class="list-decimal list-inside space-y-2">
                                <?php $__empty_1 = true; $__currentLoopData = $bestSellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li class="flex justify-between items-center text-gray-800 font-medium">
                                        <span><?php echo e($item->menu_name); ?></span>
                                        <span class="text-green-600 font-bold"><?php echo e(number_format($item->total_sold)); ?> pcs</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-sm text-gray-500">Tidak ada data terlaris yang ditemukan.</p>
                                <?php endif; ?>
                            </ol>
                        </div>

                        
                        <div class="border-2 border-red-200 p-4 rounded-xl bg-red-50 shadow-md">
                            <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center">
                                Menu Kurang Laku (Bottom 5)
                            </h4>
                            <ol class="list-decimal list-inside space-y-2">
                                <?php $__empty_1 = true; $__currentLoopData = $worstSellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li class="flex justify-between items-center text-gray-800 font-medium">
                                        <span><?php echo e($item->menu_name); ?></span>
                                        <span class="text-red-600 font-bold"><?php echo e(number_format($item->total_sold)); ?> pcs</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-sm text-gray-500">Semua menu terjual dengan baik atau tidak ada data item yang ditemukan.</p>
                                <?php endif; ?>
                            </ol>
                        </div>
                    </div>
                <?php endif; ?>
                
                
                <h3 class="text-xl font-bold mb-4">Grafik Penjualan Berdasarkan Dimensi: <?php echo e(ucfirst($groupBy)); ?></h3>
                <div class="relative h-[400px] w-full mb-10">
                    <canvas id="salesChart"></canvas>
                </div>

                
                <h3 class="text-xl font-bold mb-4 border-t pt-4">Data Detail (Drill-down)</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e($groupBy == 'menu' ? 'Nama Menu' : ucfirst($groupBy)); ?></th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $salesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 font-medium"><?php echo e($data->label ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 text-right"><?php echo e(number_format($data->total_orders)); ?></td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-700">Rp <?php echo e(number_format($data->total_revenue, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data penjualan dalam periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari Controller
        const chartLabels = <?php echo json_encode($chartLabels, 15, 512) ?>;
        const chartSalesData = <?php echo json_encode($chartData, 15, 512) ?>;
        const chartGrouping = <?php echo json_encode($groupBy, 15, 512) ?>;

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            // Hapus instance lama jika ada (untuk Livewire/refresh)
            if (window.salesChartInstance) {
                window.salesChartInstance.destroy();
            }
            
            window.salesChartInstance = new Chart(ctx, {
                type: 'bar', // Gunakan Bar Chart untuk perbandingan yang lebih baik
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: chartSalesData,
                        backgroundColor: chartGrouping === 'method' ? ['#4f46e5', '#10b981', '#f59e0b'] : '#4f46e5',
                        borderColor: '#4f46e5',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Pendapatan (Rp)' },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                             title: { display: true, text: chartGrouping === 'date' ? 'Tanggal' : (chartGrouping === 'category' ? 'Kategori' : (chartGrouping === 'menu' ? 'Nama Menu' : 'Metode Pembayaran')) }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/sales-report.blade.php ENDPATH**/ ?>