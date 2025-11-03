

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Visualisasi Tren Penjualan (30 Hari)')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-4 border-b pb-2">Grafik Pendapatan Lunas Harian</h3>

                
                <div class="relative h-[450px] w-full">
                    <canvas id="salesChart"></canvas>
                </div>
                
                
                <script>
                    // Data dari Controller (Diedit untuk kejelasan)
                    const chartLabels = <?php echo json_encode($labels, 15, 512) ?>;
                    const chartSalesData = <?php echo json_encode($data, 15, 512) ?>;

                    document.addEventListener('DOMContentLoaded', function () {
                        const ctx = document.getElementById('salesChart').getContext('2d');
                        
                        // Periksa apakah Chart sudah ada (untuk Livewire/turbolinks/caching)
                        if (window.salesChartInstance) {
                            window.salesChartInstance.destroy();
                        }
                        
                        window.salesChartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: chartLabels,
                                datasets: [{
                                    label: 'Total Penjualan (Rp)',
                                    data: chartSalesData,
                                    borderColor: 'rgb(79, 70, 229)',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Pendapatan (Rupiah)'
                                        },
                                        ticks: {
                                            callback: function(value, index, values) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    });
                </script>
                
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/charts.blade.php ENDPATH**/ ?>