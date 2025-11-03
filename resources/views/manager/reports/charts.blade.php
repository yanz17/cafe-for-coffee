@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Visualisasi Tren Penjualan (30 Hari)') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-4 border-b pb-2">Grafik Pendapatan Lunas Harian</h3>

                {{-- Canvas Chart.js --}}
                <div class="relative h-[450px] w-full">
                    <canvas id="salesChart"></canvas>
                </div>
                
                {{-- Script Chart.js --}}
                <script>
                    // Data dari Controller (Diedit untuk kejelasan)
                    const chartLabels = @json($labels);
                    const chartSalesData = @json($data);

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
@endsection