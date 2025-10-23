@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Edit Bahan Baku: ') . $bahanBaku->nama }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('manager.bahan_baku.update', $bahanBaku) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Bahan Baku</label>
                            <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('nama', $bahanBaku->nama) }}">
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unit Pengukuran</label>
                            <input type="text" name="unit" id="unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required value="{{ old('unit', $bahanBaku->unit) }}">
                            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stok_saat_ini" class="block text-sm font-medium text-gray-700">Stok Saat Ini (Untuk Restock, ubah nilai ini)</label>
                            <input type="number" name="stok_saat_ini" id="stok_saat_ini" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required min="0" value="{{ old('stok_saat_ini', $bahanBaku->stok_saat_ini) }}">
                            @error('stok_saat_ini') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="stok_minimal" class="block text-sm font-medium text-gray-700">Stok Minimal (Batas Kritis)</label>
                            <input type="number" name="stok_minimal" id="stok_minimal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required min="0" value="{{ old('stok_minimal', $bahanBaku->stok_minimal) }}">
                            @error('stok_minimal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Update Bahan Baku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection