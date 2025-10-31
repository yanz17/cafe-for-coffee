@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Kelola Kategori Menu') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                @endif
                @if (session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">{{ session('info') }}</div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        Periksa kembali input Anda.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- KARTU 1: TAMBAH KATEGORI BARU --}}
                    <div class="border p-4 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold mb-4">Tambah Baru</h3>
                        <form action="{{ route('manager.categories.store') }}" method="POST">
                            @csrf
                            <input type="text" name="new_category_name" placeholder="Nama Kategori Baru" 
                                   class="w-full rounded-md border-gray-300 shadow-sm mb-3 @error('new_category_name') border-red-500 @enderror" 
                                   required>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded">
                                Simpan Kategori
                            </button>
                        </form>
                    </div>

                    {{-- KARTU 2: DAFTAR, EDIT, HAPUS KATEGORI --}}
                    <div class="border p-4 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold mb-4">Kategori Yang Ada</h3>
                        <ul class="space-y-3">
                            @forelse ($categories as $category)
                            <li class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                <span class="font-medium">{{ $category }}</span>
                                
                                <div x-data="{ openEdit: false }">
                                    {{-- Tombol Edit --}}
                                    <button @click="openEdit = true" class="text-blue-500 hover:text-blue-700 text-sm mr-2">Edit</button>

                                    {{-- Form Hapus --}}
                                    <form action="{{ route('manager.categories.destroy') }}" method="POST" class="inline" onsubmit="return confirm('Yakin menghapus kategori {{ $category }}? Menu akan disetel ke Uncategorized.');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="category_name" value="{{ $category }}">
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                    </form>

                                    {{-- Modal Edit --}}
                                    <div x-show="openEdit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                        <div @click.away="openEdit = false" class="bg-white p-6 rounded-lg w-96">
                                            <h4 class="text-lg font-bold mb-3">Edit Kategori: {{ $category }}</h4>
                                            <form action="{{ route('manager.categories.update') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="old_category_name" value="{{ $category }}">
                                                <input type="text" name="new_category_name" value="{{ $category }}" class="w-full rounded-md border-gray-300 shadow-sm mb-3" required>
                                                <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Simpan</button>
                                                <button type="button" @click="openEdit = false" class="ml-2 bg-gray-300 py-2 px-4 rounded">Batal</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-gray-500">Belum ada kategori yang terdeteksi.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection