

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Laporan Umpan Balik Pelanggan')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">

                
                <div class="bg-white p-6 shadow-xl sm:rounded-xl">
                    <h3 class="text-xl font-bold mb-4">Filter Data Feedback</h3>
                    <form method="GET" action="<?php echo e(route('manager.reports.feedback')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai Tanggal</label>
                            <input type="date" name="start_date" id="start_date" value="<?php echo e($startDate); ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="<?php echo e($endDate); ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="rating_filter" class="block text-sm font-medium text-gray-700">Filter Rating</label>
                            <select name="rating_filter" id="rating_filter" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Semua Rating --</option>
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?php echo e($i); ?>" <?php echo e($ratingFilter == $i ? 'selected' : ''); ?>><?php echo e($i); ?> Bintang</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <button type="submit" class="w-full md:w-auto self-end bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                            Tampilkan Data
                        </button>
                    </form>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <p class="text-sm font-medium text-gray-500">Total Feedback</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1"><?php echo e(number_format($totalFeedback)); ?></p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <p class="text-sm font-medium text-gray-500">Rating Rata-rata</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1"><?php echo e(number_format($averageRating, 2)); ?> / 5.0</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-green-500">
                        <p class="text-sm font-medium text-gray-500">Feedback dengan 5 Bintang</p>
                        <?php
                            $count5 = $chartData[4] ?? 0;
                            $percentage = $totalFeedback > 0 ? ($count5 / $totalFeedback * 100) : 0;
                        ?>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1"><?php echo e(number_format($count5)); ?> <span class="text-base text-gray-500">(<?php echo e(number_format($percentage, 1)); ?>%)</span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Distribusi Rating (Bintang 1 s/d 5)</h3>
                        <div class="relative h-[300px]">
                            <canvas id="ratingChart"></canvas> 
                        </div>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Analisis Sentimen Tags Cepat</h3>
                        <div class="relative h-[300px]">
                            <canvas id="sentimentChart"></canvas> 
                        </div>
                    </div>
                </div>
                
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Top 5 Tags yang Sering Disebut</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php $__empty_1 = true; $__currentLoopData = $topTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                
                                <div class="p-3 <?php echo e(in_array($tag, $goodTags) ? 'bg-indigo-50' : 'bg-red-50'); ?> rounded-lg flex justify-between items-center">
                                    <span class="font-medium"><?php echo e($tag); ?></span>
                                    <span class="font-bold"><?php echo e($count); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="col-span-2 text-gray-500">Tidak ada tags yang cukup sering disebut.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Data Detail Feedback</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Kirim</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags Cepat</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar Tambahan</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($feedback->created_at->format('d M Y H:i')); ?>

                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-center">
                                            
                                            <span class="text-lg font-bold text-yellow-500">
                                                <?php for($i = 0; $i < $feedback->rating; $i++): ?>
                                                    ★
                                                <?php endfor; ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 max-w-sm">
                                            <?php if($feedback->tags && is_array($feedback->tags)): ?>
                                                <div class="flex flex-wrap gap-1">
                                                    <?php $__currentLoopData = $feedback->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium shadow-sm 
                                                             <?php echo e(in_array($tag, $goodTags) ? 'bg-indigo-100 text-indigo-800' : 'bg-red-100 text-red-800'); ?>">
                                                            <?php echo e($tag); ?>

                                                        </span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-800 max-w-sm overflow-hidden truncate">
                                            <?php echo e($feedback->komentar ?? 'Tidak ada'); ?>

                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            <?php echo e($feedback->user->name ?? 'N/A'); ?>

                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                            #<?php echo e($feedback->order->nomor_pesanan ?? '-'); ?>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
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
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data Grafik Rating
        const chartLabels = <?php echo json_encode($chartLabels, 15, 512) ?>;
        const chartRatingData = <?php echo json_encode($chartData, 15, 512) ?>;
        const totalFeedback = <?php echo json_encode($totalFeedback, 15, 512) ?>;
        
        // Data Grafik Tags
        const tagLabels = <?php echo json_encode(['Positif', 'Negatif'], 512) ?>;
        const tagData = <?php echo json_encode($tagChartData, 15, 512) ?>;

        document.addEventListener('DOMContentLoaded', function () {
            
            // --- GRAFIK 1: DISTRIBUSI RATING ---
            const ctxRating = document.getElementById('ratingChart').getContext('2d');
            if (window.ratingChartInstance) { window.ratingChartInstance.destroy(); }
            
            window.ratingChartInstance = new Chart(ctxRating, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Ulasan',
                        data: chartRatingData,
                        backgroundColor: (ctx) => {
                            const index = ctx.dataIndex;
                            if (index >= 3) { return 'rgba(255, 193, 7, 0.8)'; } else { return 'rgba(108, 117, 125, 0.5)'; }
                        },
                        borderColor: '#adb5bd',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return value; } }, title: { display: true, text: 'Jumlah Ulasan' } }, x: { title: { display: false } } },
                    plugins: { tooltip: { callbacks: { label: function(context) { let value = context.parsed.y; const percentage = totalFeedback > 0 ? (value / totalFeedback * 100).toFixed(1) : 0; return `${value} Ulasan (${percentage}%)`; } } }, legend: { display: false } }
                }
            });
            
            // --- GRAFIK 2: ANALISIS SENTIMEN TAGS ---
            const ctxSentiment = document.getElementById('sentimentChart').getContext('2d');
            if (window.sentimentChartInstance) { window.sentimentChartInstance.destroy(); }
            
            window.sentimentChartInstance = new Chart(ctxSentiment, {
                type: 'pie',
                data: {
                    labels: tagLabels,
                    datasets: [{
                        data: tagData,
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)', // Positif (Indigo)
                            'rgba(248, 113, 113, 0.8)' // Negatif (Red)
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        title: { display: true, text: 'Rasio Sentimen Tags' }
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cafe-for-coffee\resources\views/manager/reports/all_feedback.blade.php ENDPATH**/ ?>