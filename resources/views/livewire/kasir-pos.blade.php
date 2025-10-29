<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 flex space-x-4">
        
        {{-- Kolom 1: Daftar Menu & Search --}}
        <div class="w-1/2 bg-white shadow-xl sm:rounded-lg p-4 flex flex-col">
            <h3 class="text-xl font-semibold mb-3">Daftar Menu</h3>
            
            {{-- Search Bar (Livewire) --}}
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari menu..." 
                   class="w-full mb-4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300">

            {{-- Grid Menu --}}
            <div class="grid grid-cols-3 gap-4 overflow-y-auto flex-grow h-[75vh]">
                @forelse ($menus as $menu)
                    <div wire:click="addToCart({{ $menu->id }})"
                        class="border p-3 rounded-lg cursor-pointer transition duration-150 
                                @if ($menu->is_tersedia) hover:bg-indigo-50 @else bg-gray-100 opacity-60 cursor-not-allowed @endif"
                        @if (!$menu->is_tersedia) title="Menu tidak tersedia" @endif>
                        @if ($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}" 
                                class="w-full h-24 object-cover rounded mb-2"> {{-- UKURAN BARU: h-24 --}}
                        @endif
                        <p class="font-bold text-gray-800">{{ $menu->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $menu->kategori }}</p>
                        <p class="text-md font-semibold text-indigo-600">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">Menu tidak ditemukan.</p>
                @endforelse
            </div>
        </div>
        
        {{-- Kolom 2: Keranjang Pesanan --}}
        <div class="w-1/4 bg-white shadow-xl sm:rounded-lg p-4 flex flex-col">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Keranjang Pesanan</h3>

            <div class="space-y-3 overflow-y-auto flex-grow max-h-[70vh]">
                @forelse ($cart as $index => $item)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div class="w-1/2">
                            <p class="font-medium text-sm">{{ $item['nama'] }}</p>
                        </div>
                        <div class="flex items-center space-x-2 w-1/2 justify-end">
                            {{-- Gunakan wire:model.blur untuk update saat fokus hilang --}}
                            <input type="number" 
                                   wire:model.live.blur="cart.{{ $index }}.kuantitas"
                                   min="1" 
                                   class="w-12 border-gray-300 rounded-md text-center p-1 text-sm">
                            <p class="font-semibold text-right w-16 text-sm">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            <button wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">×</button>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 pt-5">Keranjang kosong.</p>
                @endforelse
            </div>

            <div class="mt-auto pt-4 border-t">
                <div class="flex justify-between font-bold text-xl mb-4">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- Kolom 3: Checkout & Pembayaran --}}
        <div class="w-1/4 bg-white shadow-xl sm:rounded-lg p-4">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Pembayaran</h3>

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            
            <form wire:submit.prevent="storeOrder">
                {{-- Tipe Pesanan --}}
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Tipe Pesanan</label>
                    <select wire:model.live="orderType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="dine_in">Dine In</option>
                        <option value="take_away">Take Away</option>
                    </select>
                </div>

                {{-- Nomor Meja (Hanya Dine In) --}}
                @if ($orderType === 'dine_in')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nomor Meja</label>
                        <input type="text" wire:model="meja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                @endif
                
                {{-- Metode Pembayaran --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Metode Bayar</label>
                    <select wire:model="paymentMethod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="Cash">Cash (Tunai)</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer Bank</option>
                    </select>
                </div>
                
                {{-- Jumlah Uang Diterima --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Uang Diterima (Rp)</label>
                    <input type="number" 
                           wire:model.live.debounce.300ms="amountPaid" 
                           min="{{ $this->total }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('amountPaid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- Kembalian --}}
                <div class="flex justify-between font-bold text-lg mb-6 pt-2 border-t">
                    <span>Kembalian:</span>
                    <span class="{{ $changeDue < 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($changeDue, 0, ',', '.') }}
                    </span>
                </div>

                <button type="submit" 
                    {{-- Disabled jika keranjang kosong atau uang diterima kurang --}}
                    @if (empty($cart) || $changeDue < 0) disabled @endif
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded disabled:opacity-50 transition duration-150">
                    Proses & Cetak Struk
                </button>
            </form>
        </div>
        
    </div>
</div>