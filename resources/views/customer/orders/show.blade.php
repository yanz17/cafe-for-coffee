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

                {{-- Status Bar (TETAP SAMA) --}}
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

                {{-- Tabel Item Pesanan (TETAP SAMA) --}}
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
            
            {{-- BLOK FEEDBACK INTERAKTIF (PERBAIKAN FOKUS) --}}
            <div class="mt-8 bg-gray-50 p-6 rounded-lg shadow-inner border-t">
                <h3 class="text-2xl font-bold mb-4">Umpan Balik Pelayanan</h3>

                @php
                    $feedback = \App\Models\Feedback::where('order_id', $order->id)->first();
                @endphp

                @if ($order->status_pesanan !== 'selesai')
                    <p class="text-gray-500">Anda dapat memberikan umpan balik setelah pesanan berstatus **Selesai**.</p>
                @elseif ($feedback)
                    {{-- Tampilan Feedback yang Sudah Ada (Bintang Dinamis + Tags) --}}
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-green-600 font-bold mb-2">Terima kasih! Umpan balik sudah terkirim.</p>
                        
                        {{-- DISPLAY BINTANG --}}
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

                        {{-- DISPLAY TAGS --}}
                        @if ($feedback->tags && is_array($feedback->tags))
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="text-xs font-semibold text-gray-700">Tags:</span>
                            @foreach ($feedback->tags as $tag)
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        <p class="font-semibold text-gray-700 mt-3">Komentar: "{{ $feedback->komentar ?? '-' }}"</p>
                    </div>
                @else
                    {{-- FORM UMPAN BALIK INTERAKTIF --}}
                    <div x-data="{ 
                        rating: 5, 
                        hoverRating: 0, 
                        selectedTags: [],
                        availableTags: ['Enak', 'Cepat', 'Bersih', 'Ramah', 'Nyaman', 'Suka Kopinya'] // KOREKSI: Pindah array tags ke sini
                    }">
                        <p class="text-lg font-semibold text-gray-700 mb-3 text-center">Seberapa Puaskah Anda?</p>
                        
                        {{-- KONTROL BINTANG INTERAKTIF --}}
                        <div class="flex items-center space-x-1 justify-center mb-4">
                            <template x-for="star in 5" :key="star">
                                <svg @click="rating = star"
                                     @mouseover="hoverRating = star"
                                     @mouseleave="hoverRating = 0"
                                     :class="{
                                         'text-yellow-500': star <= (hoverRating === 0 ? rating : hoverRating), 
                                         'text-gray-300': star > (hoverRating === 0 ? rating : hoverRating)
                                     }"
                                     class="h-12 w-12 cursor-pointer transition duration-150 ease-in-out fill-current transform hover:scale-105"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </template>
                        </div>

                        <p class="text-center mb-6 text-sm font-medium text-gray-500">
                            Anda memilih <span class="font-bold text-indigo-600" x-text="rating + ' Bintang'"></span>
                        </p>


                        {{-- LOGIKA TAGS INTERAKTIF BARU --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Komentar Cepat:</label>
                            <div class="flex flex-wrap gap-2">
                                
                                {{-- KOREKSI KRITIS: Looping array tags yang didefinisikan di Alpine --}}
                                <template x-for="tag in availableTags" :key="tag">
                                    <button type="button" 
                                            @click="
                                                const index = selectedTags.indexOf(tag);
                                                if (index === -1) { selectedTags.push(tag); } 
                                                else { selectedTags.splice(index, 1); }
                                            "
                                            :class="selectedTags.includes(tag) ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-indigo-100'"
                                            class="px-3 py-1 text-sm rounded-full transition duration-150"
                                            x-text="tag">
                                    </button>
                                </template>
                                {{-- AKHIR KOREKSI KRITIS --}}
                                
                            </div>
                        </div>
                        {{-- AKHIR LOGIKA TAGS --}}

                        <form action="{{ route('customer.order.feedback.store', $order) }}" method="POST">
                            @csrf
                            
                            {{-- Input tersembunyi untuk RATING dan TAGS --}}
                            <input type="hidden" name="rating" :value="rating" required>
                            <input type="hidden" name="tags" :value="JSON.stringify(selectedTags)"> {{-- KIRIM ARRAY TAGS --}}
                            
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
            {{-- AKHIR BLOK FEEDBACK --}}
            
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('customer.orders') }}" class="text-gray-500 hover:text-gray-800">&larr; Kembali ke Riwayat Pesanan</a>
        </div>
    </div>

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
@endsection

