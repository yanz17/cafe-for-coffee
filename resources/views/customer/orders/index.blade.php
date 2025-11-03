@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Riwayat Pesananku') }}</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse ($orders as $order)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 
                    {{ $order->status_pembayaran == 'lunas' && $order->status_pesanan == 'selesai' ? 'border-green-600' : 
                       ($order->status_pembayaran == 'menunggu' ? 'border-yellow-600' : 'border-indigo-600') }}">
                    
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-lg font-bold">#{{ $order->nomor_pesanan }}</span>
                        <span class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>

                    <p class="text-gray-700">Total: <span class="font-semibold text-lg text-indigo-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span></p>

                    <div class="mt-3 flex items-center space-x-3 text-sm">
                        <span class="font-medium">Status Pembayaran:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            {{ $order->status_pembayaran == 'lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($order->status_pembayaran) }}
                        </span>
                        
                        <span class="font-medium">Status Pesanan:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-800">
                            {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                        </span>
                    </div>
                    
                    @php
                        $feedback = \App\Models\Feedback::where('order_id', $order->id)->first();
                    @endphp

                    <div class="mt-4 text-right flex justify-end items-center space-x-3">
                        @if ($order->status_pesanan === 'selesai')
                            @if ($feedback) {{-- Menggunakan variabel $feedback yang baru --}}
                                {{-- Sudah memberikan feedback --}}
                                <span class="text-green-600 font-semibold text-sm">Feedback Terkirim (Rating: {{ $feedback->rating }})</span>
                            @else
                                {{-- Tombol untuk menampilkan Modal --}}
                                <button 
                                    @click="$dispatch('open-feedback-modal', { orderId: {{ $order->id }}, orderNum: '{{ $order->nomor_pesanan }}' })"
                                    class="text-red-600 hover:text-red-800 font-medium bg-red-100 py-1 px-3 rounded text-sm">
                                    Beri Feedback &rarr;
                                </button>
                            @endif
                        @endif
                        <a href="{{ route('customer.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Lihat Detail & Instruksi &rarr;</a>
                    </div>

                </div>
                @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Anda belum memiliki riwayat pesanan.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- WAJIB: SERTAKAN MODAL FEEDBACK DI SINI --}}
    @include('customer.orders.feedback_modal')

@endsection