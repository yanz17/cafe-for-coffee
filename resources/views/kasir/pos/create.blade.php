@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Antarmuka POS Kasir') }}
    </h2>
@endsection

@section('content')
    <div class="py-6" x-data="posSystem()">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Bagian Kiri: Daftar Menu --}}
            <div class="w-2/3 bg-white shadow-xl sm:rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Daftar Menu Cafe For Coffee</h3>
                
                <div class="grid grid-cols-3 gap-4 overflow-y-auto max-h-[70vh]">
                    @foreach ($menus as $menu)
                        <div @click="addItem({{ $menu->id }}, '{{ $menu->nama }}', {{ $menu->harga }})"
                            class="border p-4 rounded-lg cursor-pointer hover:bg-indigo-50 transition duration-150">
                            <p class="font-bold text-gray-800">{{ $menu->nama }}</p>
                            <p class="text-sm text-gray-500">{{ $menu->kategori }}</p>
                            <p class="text-md font-semibold text-indigo-600">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            
            {{-- Bagian Kanan: Keranjang Pesanan & Checkout --}}
            <div class="w-1/3 bg-white shadow-xl sm:rounded-lg p-4 sticky top-0">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Keranjang Pesanan</h3>

                {{-- Item Keranjang --}}
                <div class="space-y-3 overflow-y-auto max-h-[40vh]">
                    <template x-for="(item, index) in cart" :key="item.menu_id">
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium" x-text="item.nama"></p>
                                <p class="text-sm text-gray-500">@ <span x-text="formatRupiah(item.harga_satuan)"></span></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="number" x-model.number="item.kuantitas" 
                                       @input="updateQuantity(index, $event.target.value)" 
                                       min="1" class="w-16 border-gray-300 rounded-md text-center p-1">
                                <p class="font-semibold w-20 text-right" x-text="formatRupiah(item.subtotal)"></p>
                                <button @click="removeItem(index)" class="text-red-500 hover:text-red-700">×</button>
                            </div>
                        </div>
                    </template>
                    <p x-show="cart.length === 0" class="text-center text-gray-500">Keranjang kosong.</p>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between font-bold text-xl mb-4">
                        <span>Total:</span>
                        <span x-text="formatRupiah(calculateTotal())"></span>
                    </div>

                    <form action="{{ route('kasir.order.store') }}" method="POST">
                        @csrf
                        
                        {{-- Input Tersembunyi untuk Item Keranjang --}}
                        <input type="hidden" name="items" :value="JSON.stringify(cart.map(item => ({ 
                            menu_id: item.menu_id, 
                            kuantitas: item.kuantitas,
                            // catatan: item.catatan // Tambahkan ini nanti jika perlu
                        })))">

                        {{-- Tipe Pemesanan --}}
                        <div class="mb-3">
                            <label for="tipe_pemesanan" class="block text-sm font-medium text-gray-700">Tipe Pesanan</label>
                            <select name="tipe_pemesanan" id="tipe_pemesanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" x-model="orderType">
                                <option value="dine_in">Dine In</option>
                                <option value="take_away">Take Away</option>
                            </select>
                        </div>

                        {{-- Nomor Meja (Hanya untuk Dine In) --}}
                        <div class="mb-4" x-show="orderType === 'dine_in'">
                            <label for="meja" class="block text-sm font-medium text-gray-700">Nomor Meja</label>
                            <input type="text" name="meja" id="meja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <button type="submit" 
                            :disabled="cart.length === 0"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded disabled:opacity-50 transition duration-150">
                            Proses Transaksi (<span x-text="formatRupiah(calculateTotal())"></span>)
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>

    {{-- Script Alpine.js --}}
    <script>
        function posSystem() {
            return {
                cart: [],
                orderType: 'dine_in', // Default value
                
                addItem(menu_id, nama, harga) {
                    let existingItem = this.cart.find(item => item.menu_id === menu_id);

                    if (existingItem) {
                        existingItem.kuantitas++;
                        existingItem.subtotal = existingItem.kuantitas * existingItem.harga_satuan;
                    } else {
                        this.cart.push({
                            menu_id: menu_id,
                            nama: nama,
                            harga_satuan: harga,
                            kuantitas: 1,
                            subtotal: harga,
                        });
                    }
                },

                updateQuantity(index, newQuantity) {
                    if (newQuantity < 1) {
                        this.cart[index].kuantitas = 1;
                    }
                    this.cart[index].subtotal = this.cart[index].kuantitas * this.cart[index].harga_satuan;
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                },

                calculateTotal() {
                    return this.cart.reduce((total, item) => total + item.subtotal, 0);
                },
                
                formatRupiah(angka) {
                    var number_string = angka.toString(),
                        sisa    = number_string.length % 3,
                        rupiah  = number_string.substr(0, sisa),
                        ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                            
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    return 'Rp ' + rupiah;
                }
            }
        }
    </script>
@endsection