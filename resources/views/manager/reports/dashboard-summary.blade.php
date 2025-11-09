@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard Manajer') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">

                {{-- STATS UTAMA (Improved Cards) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Card 1: Penjualan Hari Ini --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 relative group transform hover:scale-[1.02] transition duration-300 border-t-4 border-indigo-500">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 uppercase">Penjualan Hari Ini</p>
                            <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 18V6"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 15v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4"></path></svg>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 mt-3">
                            Rp {{ number_format($todaySales, 0, ',', '.') }}
                        </p>
                        <a href="{{ route('manager.reports.sales') }}" class="text-xs text-indigo-500 hover:text-indigo-700 mt-2 block">Lihat Detail Laporan &rarr;</a>
                    </div>

                    {{-- Card 2: Stok Kritis --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 relative group transform hover:scale-[1.02] transition duration-300 border-t-4 {{ $criticalStockCount > 0 ? 'border-red-600' : 'border-green-600' }}">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500 uppercase">Bahan Baku Kritis</p>
                            <svg class="h-6 w-6 {{ $criticalStockCount > 0 ? 'text-red-500' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M10 17h.01"></path></svg>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 mt-3">
                            {{ $criticalStockCount }} Jenis
                        </p>
                        <a href="{{ route('manager.reports.inventory_status') }}" class="text-xs {{ $criticalStockCount > 0 ? 'text-red-600' : 'text-gray-500' }} mt-2 block font-semibold">
                            @if ($criticalStockCount > 0)
                                Perlu Restock Segera! &rarr;
                            @else
                                Stok Aman.
                            @endif
                        </a>
                    </div>
                    
                    {{-- Card 3: HUB LAPORAN DAN AKSI CEPAT --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 relative group transform hover:scale-[1.02] transition duration-300 border-t-4 border-yellow-500">
                         <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-500 uppercase">Pusat Laporan & Analisis</p>
                            <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0h2a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10"></path></svg>
                        </div>
                         
                         <ul class="space-y-1 mt-3">
                            <li class="font-semibold text-gray-800 pt-2">Manajemen Menu:</li>
                            <li>
                                <a href="{{ route('manager.menus.index') }}" class="text-sm text-yellow-600 hover:text-yellow-800">
                                    Kelola Daftar Menu
                                </a>
                            </li>
                            {{-- BARU: Link Kelola Kategori --}}
                            <li>
                                <a href="{{ route('manager.categories.index') }}" class="text-sm text-yellow-600 hover:text-yellow-800 font-bold">
                                    Kelola Kategori Menu &rarr;
                                </a>
                            </li>

                            <li class="font-semibold text-gray-800 pt-2">Business Insight:</li>
                            <li><a href="{{ route('manager.reports.customers') }}" class="text-sm text-green-600 hover:text-green-800">Segmentasi Pelanggan (Top Buyers)</a></li>
                            <li><a href="{{ route('manager.reports.recommendations') }}" class="text-sm text-green-600 hover:text-green-800">Rekomendasi Produk</a></li>
                            <li><a href="{{ route('manager.reports.inventory_status') }}" class="text-sm text-red-600 hover:text-red-800">Status Stok Kritis</a></li>
                            <li>
                                <a href="{{ route('manager.feedbacks.index') }}" 
                                class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100 
                                {{ request()->routeIs('manager.feedbacks.index') ? 'bg-gray-200 font-semibold' : '' }}">
                                    
                                    {{-- Ikon Bintang/Feedback (Anda mungkin perlu menyesuaikan icon class) --}}
                                    <svg class="w-6 h-6 text-gray-500 transition duration-75" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 2.5a.5.5 0 01.5.5v4a.5.5 0 01-1 0v-4a.5.5 0 01.5-.5zM4 9a.5.5 0 00-.5.5v1a.5.5 0 001 0v-1A.5.5 0 004 9zm12-.5a.5.5 0 01.5.5v1a.5.5 0 01-1 0v-1a.5.5 0 01.5-.5zM7.5 4a.5.5 0 000 1h5a.5.5 0 000-1h-5zM4.5 7a.5.5 0 01.5.5v.5a.5.5 0 01-1 0v-.5a.5.5 0 01.5-.5zm11 0a.5.5 0 01.5.5v.5a.5.5 0 01-1 0v-.5a.5.5 0 01.5-.5zM8 12.5a.5.5 0 01.5-.5h3a.5.5 0 010 1h-3a.5.5 0 01-.5-.5zM10 16a.5.5 0 00-.5.5v1a.5.5 0 001 0v-1a.5.5 0 00-.5-.5z" clip-rule="evenodd"></path>
                                    </svg>
                                    
                                    <span class="ml-3">Umpan Balik Pelanggan</span>
                                </a>
                            </li>
                         </ul>
                    </div>
                </div>

                {{-- TRANSAKSI TERAKHIR --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Transaksi Lunas Terbaru (Top 5)</h3>
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Nomor Pesanan</th>
                                    <th class="px-6 py-3 text-left">Waktu</th>
                                    <th class="px-6 py-3 text-left">Pelanggan</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3 text-center">Metode Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 font-medium">{{ $order->nomor_pesanan }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $order->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $order->user->name ?? 'Kasir Langsung' }}</td>
                                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment_method_final === 'Cash' ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $order->payment_method_final ?? 'POS' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada transaksi yang tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection