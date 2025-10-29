@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Edit Menu: ') . $menu->nama }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('manager.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- WAJIB: Menggunakan method PUT untuk update --}}

                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Menu</label>
                            <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('nama', $menu->nama) }}">
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                            <input type="number" name="harga" id="harga" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required min="0" value="{{ old('harga', $menu->harga) }}">
                            @error('harga') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="kategori" id="kategori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                {{-- Gunakan data dari database untuk opsi yang terpilih --}}
                                @foreach (['Coffee', 'Non-Coffee', 'Snack'] as $category)
                                    <option value="{{ $category }}" {{ old('kategori', $menu->kategori) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                            @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                            @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="is_tersedia" class="inline-flex items-center">
                                <input type="hidden" name="is_tersedia" value="0"> {{-- Hidden field untuk memastikan nilai 0 terkirim --}}
                                <input type="checkbox" name="is_tersedia" id="is_tersedia" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="1" {{ old('is_tersedia', $menu->is_tersedia) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">Tersedia untuk dijual</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="block text-sm font-medium text-gray-700">Foto Menu (Max 2MB)</label>
                            @if ($menu->foto)
                                <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}" class="h-24 w-24 object-cover mb-2 rounded">
                                <p class="text-xs text-gray-500 mb-1">Ganti foto yang sudah ada.</p>
                            @endif
                            <input type="file" name="foto" id="foto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Perbarui Menu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection