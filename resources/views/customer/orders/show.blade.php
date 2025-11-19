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
        $feedback = \App\Models\Feedback::where('order_id', $order->id)->first(); // Anda perlu menambahkan relasi feedback ke model Order
    @endphp

    {{-- Bagian Umpan Balik --}}
    <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow-inner border-t">
        <h3 class="text-2xl font-bold mb-4">Umpan Balik Pelayanan</h3>

        @if ($order->status_pesanan !== 'selesai')
            <p class="text-gray-500">Anda dapat memberikan umpan balik setelah pesanan berstatus **Selesai**.</p>
        @elseif ($feedback)
            {{-- Tampilan Feedback yang Sudah Ada --}}
            <div class="border-l-4 border-green-500 pl-4">
                <p class="text-green-600 font-bold mb-2">Terima kasih! Umpan balik sudah terkirim.</p>
                <p class="font-semibold">Rating Anda: {{ $feedback->rating }} Bintang</p>
                <p class="text-gray-700 mt-1">Komentar: "{{ $feedback->komentar ?? '-' }}"</p>
            </div>
        @else
            {{-- Form Umpan Balik (Hanya tampil jika Selesai dan Belum Ada Feedback) --}}
            <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow-inner border-t">
                <h3 class="text-2xl font-bold mb-4">Umpan Balik Pelayanan</h3>

                @php
                    // Menggunakan Query Builder yang aman
                    $feedback = \App\Models\Feedback::where('order_id', $order->id)->first();
                @endphp

                @if ($order->status_pesanan !== 'selesai')
                    <p class="text-gray-500">Anda dapat memberikan umpan balik setelah pesanan berstatus **Selesai**.</p>
                @elseif ($feedback)
                    {{-- Tampilan Feedback yang Sudah Ada (Bintang Dinamis) --}}
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-green-600 font-bold mb-2">Terima kasih! Umpan balik sudah terkirim.</p>
                        
                        {{-- DISPLAY BINTANG BERDASARKAN RATING --}}
                        <div class="flex items-center space-x-1 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="h-8 w-8 transition duration-150 ease-in-out fill-current 
                                    @if ($feedback->rating >= $i) text-yellow-500 @else text-gray-300 @endif" 
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            @endfor
                            <span class="text-sm font-semibold ml-2 text-gray-700">({{ $feedback->rating }} Bintang)</span>
                        </div>
                        
                        <p class="font-semibold text-gray-700 mt-1">Komentar: "{{ $feedback->komentar ?? '-' }}"</p>
                    </div>
                @else
                    {{-- FORM UMPAN BALIK INTERAKTIF DENGAN BINTANG --}}
                    <div x-data="{ rating: 5, hoverRating: 0 }">
                        <p class="text-lg font-semibold text-gray-700 mb-3 text-center">Seberapa Puaskah Anda?</p>
                        
                        {{-- KONTROL BINTANG INTERAKTIF --}}
                        <div class="flex items-center space-x-1 justify-center mb-4">
                            <template x-for="star in 5" :key="star">
                                <svg @click="rating = star" {{-- KUNCI: Bintang menjadi tombol yang mengatur 'rating' --}}
                                     @mouseover="hoverRating = star"
                                     @mouseleave="hoverRating = 0"
                                     :class="{
                                         // Logika warna: Gunakan hoverRating jika > 0, jika tidak gunakan rating
                                         'text-yellow-500': star <= (hoverRating === 0 ? rating : hoverRating), 
                                         'text-gray-300': star > (hoverRating === 0 ? rating : hoverRating)
                                     }"
                                     class="h-12 w-12 cursor-pointer transition duration-150 ease-in-out fill-current transform hover:scale-105"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </template>
                        </div>

                        <p class="text-center mb-4 text-sm font-medium text-gray-500">
                            Anda memilih <span class="font-bold text-indigo-600" x-text="rating + ' Bintang'"></span>
                        </p>


                        <form action="{{ route('customer.order.feedback.store', $order) }}" method="POST">
                            @csrf
                            
                            {{-- Input tersembunyi untuk mengirim nilai rating --}}
                            <input type="hidden" name="rating" :value="rating" required>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Komentar (Opsional)</label>
                                <textarea name="komentar" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Komentar Anda..."></textarea>
                                @error('komentar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded shadow-md">
                                Kirim Umpan Balik
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@if ($order->status_pembayaran == 'menunggu' && $order->snap_token)
    
    {{-- Tombol Lanjutkan Pembayaran (Jika pop-up tertutup) --}}
    <div class="mt-6 text-center">
        <button id="pay-button" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg">
            Lanjutkan Pembayaran
        </button>
        <p class="text-sm text-gray-500 mt-2">Jika pop-up tidak muncul otomatis, klik tombol di atas.</p>
    </div>

    {{-- SCRIPT MIDTRANS SNAPS --}}
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    <script type="text/javascript">
        const snapToken = '{{ $order->snap_token }}';

        document.addEventListener('DOMContentLoaded', function () {
            // Panggil pembayaran secara otomatis saat halaman dimuat
            if (snapToken) {
                // Panggil pop-up Midtrans
                window.snap.pay(snapToken, {
                    onSuccess: function(result){
                        alert("Pembayaran Berhasil!");
                        window.location.reload(); 
                    },
                    onPending: function(result){
                        alert("Pembayaran Anda sedang diproses.");
                        window.location.reload(); 
                    },
                    onClose: function(){
                        // Opsional: Jika user menutup pop-up tanpa menyelesaikan pembayaran
                        console.log('Pembayaran ditutup.');
                    }
                });
            }
        });
        
        // Listener untuk tombol manual
        document.getElementById('pay-button').onclick = function () {
            window.snap.pay(snapToken);
        };
    </script>
@endif