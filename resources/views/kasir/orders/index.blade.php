@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Daftar Pesanan & Konfirmasi Pembayaran') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('kasir.orders.index') }}">
                    <div class="flex space-x-2">
                        <input type="text" name="search" placeholder="Cari No. Pesanan atau Scan Kode..." 
                            class="flex-grow rounded-md border-gray-300 shadow-sm" 
                            value="{{ $search }}">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md">Cari</button>
                        @if($search)
                            <a href="{{ route('kasir.orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="space-y-6">

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
                @endif

                {{-- Bagian 1: Pesanan Menunggu Konfirmasi Pembayaran (Online/QRIS) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2 text-yellow-700">
                            Pesanan Menunggu Pembayaran (Online/Transfer) ({{ $pendingOrders->count() }})
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse ($pendingOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 font-medium">#{{ $order->nomor_pesanan }}</td>
                                    <td class="px-6 py-4">{{ $order->user->name ?? 'Kasir Input' }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                    
                                    {{-- BARIS YANG HILANG --}}
                                    <td class="px-6 py-4">{{ $order->status_pembayaran }}</td>
                                    <td class="px-6 py-4">{{ $order->tipe_pemesanan }}</td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        {{-- Tombol ACC Pembayaran/Proses --}}
                                        <form action="{{ route('kasir.orders.process', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin pesanan ini sudah dibayar dan siap diproses?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                ACC & Proses
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <p class="text-gray-500 col-span-3">Tidak ada pesanan menunggu pembayaran saat ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Pesanan yang Sedang Diproses --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2 text-indigo-700">
                            Pesanan Sedang Diproses ({{ $activeOrders->count() }})
                        </h3>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">No. Pesanan</th>
                                    <th class="px-6 py-3 text-left">Pelanggan</th> <th class="px-6 py-3 text-left">Total</th>
                                    <th class="px-6 py-3 text-left">Metode Bayar</th>
                                    <th class="px-6 py-3 text-left">Status Pesanan</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($activeOrders as $order)
                                    <tr>
                                        <td class="px-6 py-4 font-medium">#{{ $order->nomor_pesanan }}</td>
                                        <td class="px-6 py-4">{{ $order->user->name ?? 'Kasir Input' }}</td> 
                                        <td class="px-6 py-4">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $order->payment_method_final ?? 'Belum Lunas' }}</td> 
                                        <td class="px-6 py-4 text-sm">{{ $order->status_pesanan }}</td>
                                        <td class="px-6 py-4 text-center">
                                            {{-- Tombol Selesaikan Pesanan --}}
                                            <form action="{{ route('kasir.orders.complete', $order) }}" method="POST" onsubmit="return confirm('Selesaikan pesanan #{{ $order->nomor_pesanan }}? Stok akan dikurangi.');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Selesaikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bagian 3: Riwayat Pesanan yang Selesai --}}
                <hr class="my-8">

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pesanan Selesai ({{ $completedOrders->count() }})</h2>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">No. Pesanan</th>
                                    <th class="px-6 py-3 text-left">Pelanggan</th>
                                    <th class="px-6 py-3 text-left">Total</th>
                                    <th class="px-6 py-3 text-left">Selesai Pada</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($completedOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 font-medium">#{{ $order->nomor_pesanan }}</td>
                                    <td class="px-6 py-4">{{ $order->user->name ?? 'Kasir Input' }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $order->updated_at->format('d/m H:i') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        {{-- Tombol Pindahkan Kembali --}}
                                        <form action="{{ route('kasir.orders.revert', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan status Selesai dan memproses kembali pesanan #{{ $order->nomor_pesanan }}?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Pindahkan Kembali
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pesanan yang selesai.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection