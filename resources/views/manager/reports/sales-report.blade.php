@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Laporan Penjualan OLAP & Visualisasi') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold mb-6">Analisis Penjualan Dinamis</h3>

                {{-- FORM FILTER & ROLL-UP (SLICING & DICING) --}}
                
                <form method="GET" action="{{ route('manager.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end mb-8 p-4 border rounded-lg bg-gray-50">
                    
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-lg border-gray-300">
                    </div>

                    {{-- DIMENSI GROUPING (ROLL-UP) --}}
                    <div>
                        <label for="group_by" class="block text-sm font-medium text-gray-700">Kelompokkan Berdasarkan (Roll-up)</label>
                        <select name="group_by" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="date" {{ $groupBy == 'date' ? 'selected' : '' }}>Tanggal Harian</option>
                            <option value="category" {{ $groupBy == 'category' ? 'selected' : '' }}>Kategori Menu</option>
                            <option value="method" {{ $groupBy == 'method' ? 'selected' : '' }}>Metode Pembayaran</option>
                            <option value="menu" {{ $groupBy == 'menu' ? 'selected' : '' }}>Nama Menu</option> {{-- BARU: Opsi Nama Menu --}}
                        </select>
                    </div>

                    {{-- SLICING/DICING FILTER --}}
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Filter Metode Bayar</label>
                        <select name="payment_method" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">-- Semua Metode --</option>
                            @foreach (['Cash', 'QRIS', 'Transfer'] as $method)
                                <option value="{{ $method }}" {{ $paymentMethod == $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full md:w-auto self-end bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                        Tampilkan Data
                    </button>
                </form>

                <div class="mb-8 p-4 border rounded-lg bg-indigo-50 border-indigo-200">
                    <h4 class="font-bold text-indigo-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Import Data Penjualan dari Excel
                    </h4>
                    <form action="{{ route('manager.reports.sales.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                        @csrf
                        <input type="file" name="file_excel" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" required>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                            Upload & Import
                        </button>
                    </form>
                    <p class="text-[10px] text-indigo-400 mt-2">*Format kolom harus: nomor_pesanan, nama_menu, kuantitas, total_harga, metode_bayar, tanggal</p>
                </div>

                <hr class="mb-8">

                {{-- RINGKASAN MENU LARIS & KURANG LAKU (BARU) --}}
                @if ($groupBy === 'menu')
                    <h3 class="text-xl font-bold mb-4">📈 Ringkasan Popularitas Menu ({{ $startDate }} s/d {{ $endDate }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                        
                        {{-- Menu Terlaris --}}
                        <div class="border-2 border-green-200 p-4 rounded-xl bg-green-50 shadow-md">
                            <h4 class="text-lg font-semibold text-green-700 mb-3 flex items-center">
                                Menu Paling Laris (Top 5)
                            </h4>
                            <ol class="list-decimal list-inside space-y-2">
                                @forelse ($bestSellers as $item)
                                    <li class="flex justify-between items-center text-gray-800 font-medium">
                                        <span>{{ $item->menu_name }}</span>
                                        <span class="text-green-600 font-bold">{{ number_format($item->total_sold) }} pcs</span>
                                    </li>
                                @empty
                                    <p class="text-sm text-gray-500">Tidak ada data terlaris yang ditemukan.</p>
                                @endforelse
                            </ol>
                        </div>

                        {{-- Menu Kurang Laku --}}
                        <div class="border-2 border-red-200 p-4 rounded-xl bg-red-50 shadow-md">
                            <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center">
                                Menu Kurang Laku (Bottom 5)
                            </h4>
                            <ol class="list-decimal list-inside space-y-2">
                                @forelse ($worstSellers as $item)
                                    <li class="flex justify-between items-center text-gray-800 font-medium">
                                        <span>{{ $item->menu_name }}</span>
                                        <span class="text-red-600 font-bold">{{ number_format($item->total_sold) }} pcs</span>
                                    </li>
                                @empty
                                    <p class="text-sm text-gray-500">Semua menu terjual dengan baik atau tidak ada data item yang ditemukan.</p>
                                @endforelse
                            </ol>
                        </div>
                    </div>
                @endif
                
                {{-- VISUALISASI DATA (GRAFIK) --}}
                <h3 class="text-xl font-bold mb-4">Grafik Penjualan Berdasarkan Dimensi: {{ ucfirst($groupBy) }}</h3>
                <div class="relative h-[400px] w-full mb-10">
                    <canvas id="salesChart"></canvas>
                </div>

                {{-- TABEL DATA (DRILL-DOWN) --}}
                <h3 class="text-xl font-bold mb-4 border-t pt-4">Data Detail (Drill-down)</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $groupBy == 'menu' ? 'Nama Menu' : ucfirst($groupBy) }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($salesData as $data)
                        <tr>
                            <td class="px-6 py-4 font-medium">{{ $data->label ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($data->total_orders) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-700">Rp {{ number_format($data->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data penjualan dalam periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    
    {{-- SCRIPT CHART.JS UNTUK VISUALISASI DINAMIS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari Controller
        const chartLabels = @json($chartLabels);
        const chartSalesData = @json($chartData);
        const chartGrouping = @json($groupBy);

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
@endsection