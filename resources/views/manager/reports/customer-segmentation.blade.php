@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Laporan Segmentasi Pelanggan') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-10">

                {{-- Section: Top Spenders --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border-t-4 border-indigo-500">
                    <h3 class="text-2xl font-bold mb-4">Top 10 Pelanggan Terbaik (Total Pengeluaran 90 Hari)</h3>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Pelanggan</th>
                                <th class="px-6 py-3 text-right">Total Belanja (Rp)</th>
                                <th class="px-6 py-3 text-center">Jumlah Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topSpenders as $user)
                            <tr class="hover:bg-indigo-50/50 transition">
                                <td class="px-6 py-4 font-medium">{{ $user->name }} ({{ $user->email }})</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-700">Rp {{ number_format($user->total_spent, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">{{ $user->total_orders }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada data pelanggan yang tersegmen.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Section: Frequent Buyers --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border-t-4 border-green-500">
                    <h3 class="text-2xl font-bold mb-4">Top 10 Pembeli Paling Sering (90 Hari)</h3>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Pelanggan</th>
                                <th class="px-6 py-3 text-center">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right">Total Belanja (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($frequentBuyers as $user)
                            <tr class="hover:bg-green-50/50 transition">
                                <td class="px-6 py-4 font-medium">{{ $user->name }} ({{ $user->email }})</td>
                                <td class="px-6 py-4 text-center font-bold text-green-700">{{ $user->total_orders }}</td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($user->total_spent, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada data pelanggan yang tersegmen.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection