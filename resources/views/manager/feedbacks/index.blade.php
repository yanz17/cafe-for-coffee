@extends('layouts.app') 
{{-- Sesuaikan layout utama Anda (misalnya layouts.manager) --}}

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Semua Umpan Balik Pelanggan') }}</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">Daftar Feedback (Total: {{ $feedbacks->total() }})</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Kirim</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pesanan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($feedbacks as $feedback)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $feedback->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- Tampilkan bintang berdasarkan rating --}}
                                        <span class="text-lg font-bold {{ $feedback->rating >= 4 ? 'text-green-500' : 'text-yellow-500' }}">
                                            @for ($i = 0; $i < $feedback->rating; $i++)
                                                ★
                                            @endfor
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800 max-w-xs overflow-hidden truncate">
                                        {{ $feedback->komentar ?? 'Tidak ada komentar' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        {{ $feedback->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #{{ $feedback->order->nomor_pesanan ?? 'N/A' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada umpan balik yang tersedia.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $feedbacks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection