@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Visualisasi Penjualan (30 Hari)') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-4 border-b pb-2">Tren Pendapatan Harian</h3>

                {{-- Canvas Chart.js --}}
                <div class="relative h-[400px]">
                    <canvas id="salesChart"></canvas>
                </div>
                
                {{-- Script Chart.js --}}
                <script>
                    // Data dari Controller
                    const labels = @json($labels);
                    const salesData = @json($data);

                    document.addEventListener('DOMContentLoaded', function () {
                        const ctx = document.getElementById('salesChart').getContext('2d');
                        
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Penjualan (Rp)',
                                    data: salesData,
                                    borderColor: 'rgb(79, 70, 229)', // Indigo color
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    tension: 0.2,
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
                                            text: 'Pendapatan (Rp)'
                                        },
                                        ticks: {
                                            // Format Rupiah di Sumbu Y
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