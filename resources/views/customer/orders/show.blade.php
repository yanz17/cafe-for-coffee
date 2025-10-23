@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Detail Pesanan #') . $order->nomor_pesanan }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Status Bar --}}
                <div class="mb-6 p-4 rounded-lg 
                    @if ($order->status_pembayaran == 'lunas') bg-green-100 border-green-400 text-green-700
                    @elseif ($order->status_pembayaran == 'menunggu') bg-yellow-100 border-yellow-400 text-yellow-700
                    @else bg-gray-100 border-gray-400 text-gray-700
                    @endif border-l-4">
                    
                    <h4 class="text-lg font-bold">Status Pembayaran: {{ ucfirst($order->status_pembayaran) }}</h4>
                    <p class="text-sm">Status Pesanan: {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}</p>

                    @if ($order->status_pembayaran == 'menunggu')
                        <p class="mt-2 font-semibold">Instruksi: Silakan datang ke Kasir Cafe For Coffee dan sebutkan **Nomor Pesanan Anda ({{ $order->nomor_pesanan }})** untuk menyelesaikan pembayaran.</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <h5 class="font-bold text-gray-700">Tipe Pesanan</h5>
                        <p class="capitalize">{{ str_replace('_', ' ', $order->tipe_pemesanan) }} 
                            @if ($order->meja) (Meja No. {{ $order->meja }}) @endif</p>
                    </div>
                    <div>
                        <h5 class="font-bold text-gray-700">Waktu Pesan</h5>
                        <p>{{ $order->created_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>

                {{-- Tabel Item Pesanan --}}
                <h3 class="text-xl font-bold border-t pt-4 mt-4 mb-3">Item Pesanan</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Menu</th>
                            <th class="px-6 py-3 text-center">Qty</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">{{ $item->menu->nama }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->kuantitas }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Total --}}
                <div class="flex justify-end mt-4">
                    <div class="w-1/2 md:w-1/3">
                        <div class="flex justify-between font-bold text-xl border-t pt-2">
                            <span>TOTAL AKHIR:</span>
                            <span class="text-indigo-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('customer.orders') }}" class="text-gray-500 hover:text-gray-800">&larr; Kembali ke Riwayat Pesanan</a>
            </div>
        </div>
    </div>
    <script>
        @if (session('clear_cart'))
            localStorage.removeItem('cafe_for_coffee_cart');
        @endif
    </script>

    {{-- Load feedback yang sudah ada --}}
    @php
        //$feedback = $order->feedback; // Anda perlu menambahkan relasi feedback ke model Order
    @endphp

    {{-- Bagian Umpan Balik --}}
    <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow-inner">
        <h3 class="text-xl font-bold mb-4">Berikan Umpan Balik</h3>

        @if ($order->status_pesanan !== 'selesai')
            <p class="text-gray-500">Umpan balik dapat diberikan setelah pesanan berstatus Selesai.</p>
        @elseif ($feedback)
            <p class="text-green-600 font-semibold">Anda sudah memberikan rating {{ $feedback->rating }} bintang. Terima kasih!</p>
            <p class="text-gray-700 mt-2">Komentar: "{{ $feedback->komentar ?? '-' }}"</p>
        @else
            {{-- Form Umpan Balik --}}
            <form action="{{ route('customer.order.feedback.store', $order) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Rating (1-5)</label>
                    <input type="number" name="rating" min="1" max="5" required class="mt-1 block w-20 rounded-md border-gray-300 shadow-sm" value="{{ old('rating') }}">
                    @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Komentar (Opsional)</label>
                    <textarea name="komentar" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('komentar') }}</textarea>
                    @error('komentar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Kirim Umpan Balik
                </button>
            </form>
        @endif
    </div>
@endsection