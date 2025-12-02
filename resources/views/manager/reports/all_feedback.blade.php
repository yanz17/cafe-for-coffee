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

                {{-- GRAFIK DISTRIBUSI RATING (TETAP SAMA) --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Distribusi Rating (Bintang 1 s/d 5)</h3>
                    <div class="relative h-[300px]">
                        <canvas id="feedbackChart"></canvas>
                    </div>
                </div>


                {{-- TABEL DATA DETAIL (Penambahan Kolom Tags dan Komentar) --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Data Detail Feedback</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Kirim</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                        
                                        {{-- KOLOM BARU --}}
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
                                        
                                        {{-- DATA BARU: TAGS CEPAT --}}
                                        <td class="px-3 py-4 max-w-sm">
                                            @if ($feedback->tags && is_array($feedback->tags))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($feedback->tags as $tag)
                                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">N/A</span>
                                            @endif
                                        </td>
                                        
                                        {{-- DATA BARU: KOMENTAR TAMBAHAN --}}
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
    
    {{-- SCRIPT CHART.JS UNTUK VISUALISASI DINAMIS (TETAP SAMA) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartFeedbackLabels = @json($chartLabels);
        const chartFeedbackData = @json($chartData);
        const totalFeedback = @json($totalFeedback);

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('feedbackChart').getContext('2d');
            
            if (window.feedbackChartInstance) {
                window.feedbackChartInstance.destroy();
            }
            
            window.feedbackChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartFeedbackLabels,
                    datasets: [{
                        label: 'Jumlah Feedback',
                        data: chartFeedbackData,
                        backgroundColor: (ctx) => {
                            const index = ctx.dataIndex;
                            if (index >= 3) {
                                return 'rgba(255, 193, 7, 0.8)';
                            } else {
                                return 'rgba(108, 117, 125, 0.5)';
                            }
                        },
                        borderColor: '#adb5bd',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value;
                                }
                            },
                            title: { display: true, text: 'Jumlah Ulasan' }
                        },
                        x: {
                             title: { display: false }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const value = context.parsed.y;
                                    const percentage = totalFeedback > 0 ? (value / totalFeedback * 100).toFixed(1) : 0;
                                    return `${value} Ulasan (${percentage}%)`;
                                }
                            }
                        },
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
@endsection