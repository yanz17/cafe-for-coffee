@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Laporan Status Inventaris') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                
                <h3 class="text-2xl font-bold text-red-600 mb-4">Stok Kritis (Perlu Order Ulang: {{ $criticalStock->count() }} item)</h3>
                
                <table class="min-w-full divide-y divide-gray-200 mb-8">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama Bahan Baku</th>
                            <th class="px-6 py-3 text-right">Stok Saat Ini</th>
                            <th class="px-6 py-3 text-right">Stok Minimal</th>
                            <th class="px-6 py-3 text-right">Kurang</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($criticalStock as $bahan)
                        <tr class="bg-red-50/50">
                            <td class="px-6 py-4 font-medium">{{ $bahan->nama }} ({{ $bahan->unit }})</td>
                            <td class="px-6 py-4 text-right text-red-600 font-bold">{{ number_format($bahan->stok_saat_ini) }} {{ $bahan->unit }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($bahan->stok_minimal) }} {{ $bahan->unit }}</td>
                            <td class="px-6 py-4 text-right text-red-600 font-bold">{{ number_format($bahan->stok_minimal - $bahan->stok_saat_ini) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-green-600">Semua stok bahan baku di atas batas minimal.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Tabel Semua Stok --}}
                <h3 class="text-2xl font-bold mb-4 border-t pt-4">Semua Bahan Baku</h3>
                 <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama Bahan Baku</th>
                            <th class="px-6 py-3 text-right">Stok Saat Ini</th>
                            <th class="px-6 py-3 text-right">Stok Minimal</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                         @foreach ($allStock as $bahan)
                         <tr>
                            <td class="px-6 py-4 font-medium">{{ $bahan->nama }} ({{ $bahan->unit }})</td>
                            <td class="px-6 py-4 text-right">{{ number_format($bahan->stok_saat_ini) }} {{ $bahan->unit }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($bahan->stok_minimal) }} {{ $bahan->unit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bahan->stok_saat_ini <= $bahan->stok_minimal ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $bahan->stok_saat_ini <= $bahan->stok_minimal ? 'KRITIS' : 'AMAN' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection