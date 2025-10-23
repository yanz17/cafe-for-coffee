@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Laporan Popularitas Produk') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">
                
                <div class="bg-white p-6 shadow-xl sm:rounded-xl">
                    <h3 class="text-xl font-bold mb-4">Filter Periode</h3>
                    <form method="GET" action="{{ route('manager.reports.popularity') }}" class="flex items-end space-x-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai Tanggal</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">Tampilkan</button>
                    </form>
                </div>
                
                {{-- RINGKASAN KATEGORI (ROLL-UP) --}}
                <h3 class="text-2xl font-bold border-b pb-2">Ringkasan Kategori Terlaris</h3>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($categorySummary as $kategori => $summary)
                        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-lg shadow-sm">
                            <p class="text-sm font-medium text-indigo-700 uppercase">{{ $kategori }}</p>
                            <p class="text-3xl font-extrabold mt-1">{{ number_format($summary['total_items']) }} pcs</p>
                            <p class="text-xs text-gray-500">{{ $summary['item_count'] }} jenis menu</p>
                        </div>
                    @endforeach
                </div>
                
                {{-- DETAIL PRODUK (DRILL-DOWN LEVEL) --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4">Detail Kuantitas Terjual</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Nama Menu</th>
                                    <th class="px-6 py-3 text-left">Kategori</th>
                                    <th class="px-6 py-3 text-right">Total Terjual (pcs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($popularity as $item)
                                <tr>
                                    <td class="px-6 py-4 font-medium">{{ $item->nama }}</td>
                                    <td class="px-6 py-4">{{ $item->kategori }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-lg">{{ number_format($item->total_sold) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection