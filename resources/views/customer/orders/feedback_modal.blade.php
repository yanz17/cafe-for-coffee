<div x-data="{ open: false, orderId: null, orderNum: '', rating: 5 }" 
     @open-feedback-modal.window="open = true; orderId = $event.detail.orderId; orderNum = $event.detail.orderNum;"
     x-show="open" 
     class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50"
     x-cloak>
    
    {{-- MODAL CARD --}}
    <div @click.away="open = false" class="bg-white p-8 rounded-xl w-full max-w-lg shadow-2xl transform transition-all duration-300 ease-in-out">
        
        <h3 class="text-2xl font-extrabold text-indigo-700 mb-2">Beri Kami Feedback</h3>
        <p class="text-gray-600 mb-6 border-b pb-3">Pesanan #<span x-text="orderNum" class="font-semibold"></span>. Masukan Anda sangat berarti!</p>
        
        <form :action="`/order/${orderId}/feedback`" method="POST">
            @csrf
            
            {{-- 1. RATING BINTANG VISUAL --}}
            <div class="mb-6">
                <label class="block text-lg font-semibold text-gray-700 mb-3">Seberapa Puaskah Anda?</label>
                
                <div class="flex items-center space-x-2 justify-center">
                    {{-- Loop untuk 5 bintang --}}
                    <template x-for="star in 5" :key="star">
                        <svg @click="rating = star"
                             :class="{'text-yellow-400': star <= rating, 'text-gray-300': star > rating}"
                             class="h-10 w-10 cursor-pointer transition duration-150 ease-in-out fill-current stroke-current"
                             viewBox="0 0 24 24">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                    </template>
                </div>
                
                <input type="hidden" name="rating" :value="rating" required>
                @error('rating') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
            </div>
            
            {{-- 2. KOMENTAR --}}
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2" for="komentar">Komentar Tambahan (Opsional)</label>
                <textarea name="komentar" id="komentar" rows="4" 
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Apa pendapat Anda tentang layanan kami?"></textarea>
                @error('komentar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            {{-- 3. TOMBOL AKSI --}}
            <div class="flex justify-between space-x-3">
                <button type="button" @click="open = false" class="flex-1 bg-gray-200 hover:bg-gray-300 py-3 px-4 rounded-lg font-medium transition duration-150">Batal</button>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-bold transition duration-150 shadow-md">
                    Kirim Feedback
                </button>
            </div>
        </form>
    </div>
</div>