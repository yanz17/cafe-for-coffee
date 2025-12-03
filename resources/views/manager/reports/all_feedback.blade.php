@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Laporan Umpan Balik Pelanggan') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">

                {{-- FORM FILTER BERDASARKAN TANGGAL DAN RATING (TETAP SAMA) --}}
                <div class="bg-white p-6 shadow-xl sm:rounded-xl">
                    <h3 class="text-xl font-bold mb-4">Filter Data Feedback</h3>
                    <form method="GET" action="{{ route('manager.reports.feedback') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai Tanggal</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="rating_filter" class="block text-sm font-medium text-gray-700">Filter Rating</label>
                            <select name="rating_filter" id="rating_filter" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Semua Rating --</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ $ratingFilter == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit" class="w-full md:w-auto self-end bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                            Tampilkan Data
                        </button>
                    </form>
                </div>

                {{-- KARTU STATISTIK UTAMA (TETAP SAMA) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <p class="text-sm font-medium text-gray-500">Total Feedback</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalFeedback) }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <p class="text-sm font-medium text-gray-500">Rating Rata-rata</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($averageRating, 2) }} / 5.0</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-green-500">
                        <p class="text-sm font-medium text-gray-500">Feedback dengan 5 Bintang</p>
                        @php
                            $count5 = $chartData[4] ?? 0;
                            $percentage = $totalFeedback > 0 ? ($count5 / $totalFeedback * 100) : 0;
                        @endphp
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($count5) }} <span class="text-base text-gray-500">({{ number_format($percentage, 1) }}%)</span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- KOTAK 1: GRAFIK DISTRIBUSI RATING --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Distribusi Rating (Bintang 1 s/d 5)</h3>
                        <div class="relative h-[300px]">
                            <canvas id="ratingChart"></canvas> {{-- ID Diubah --}}
                        </div>
                    </div>

                    {{-- KOTAK 2: GRAFIK TAG POSITIF VS NEGATIF --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Analisis Sentimen Tags Cepat</h3>
                        <div class="relative h-[300px]">
                            <canvas id="sentimentChart"></canvas> {{-- CHART BARU --}}
                        </div>
                    </div>
                </div>
                
                {{-- TABEL TOP TAGS --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Top 5 Tags yang Sering Disebut</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @forelse ($topTags as $tag => $count)
                                {{-- KOREKSI UTAMA: Menggunakan in_array dengan $goodTags untuk pewarnaan --}}
                                <div class="p-3 {{ in_array($tag, $goodTags) ? 'bg-indigo-50' : 'bg-red-50' }} rounded-lg flex justify-between items-center">
                                    <span class="font-medium">{{ $tag }}</span>
                                    <span class="font-bold">{{ $count }}</span>
                                </div>
                            @empty
                                <p class="col-span-2 text-gray-500">Tidak ada tags yang cukup sering disebut.</p>
                            @endforelse
                        </div>
                    </div>
                </div>


                {{-- TABEL DATA DETAIL (TETAP SAMA) --}}
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
                                    @forelse ($feedbacks as $feedback)
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $feedback->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-center">
                                            {{-- Display Bintang Visual --}}
                                            <span class="text-lg font-bold text-yellow-500">
                                                @for ($i = 0; $i < $feedback->rating; $i++)
                                                    ★
                                                @endfor
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 max-w-sm">
                                            @if ($feedback->tags && is_array($feedback->tags))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($feedback->tags as $tag)
                                                        {{-- KOREKSI KRITIS: Gunakan in_array untuk mewarnai tags di tabel detail --}}
                                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium shadow-sm 
                                                             {{ in_array($tag, $goodTags) ? 'bg-indigo-100 text-indigo-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-800 max-w-sm overflow-hidden truncate">
                                            {{ $feedback->komentar ?? 'Tidak ada' }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            {{ $feedback->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                            #{{ $feedback->order->nomor_pesanan ?? '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada umpan balik yang tersedia.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $feedbacks->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    
    {{-- SCRIPT CHART.JS UNTUK VISUALISASI DINAMIS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data Grafik Rating
        const chartLabels = @json($chartLabels);
        const chartRatingData = @json($chartData);
        const totalFeedback = @json($totalFeedback);
        
        // Data Grafik Tags
        const tagLabels = @json(['Positif', 'Negatif']);
        const tagData = @json($tagChartData);

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
@endsection