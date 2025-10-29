@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Kelola Resep: ') . $menu->nama }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                {{-- Menyimpan data JSON mentah untuk diakses Alpine --}}
                <script id="existing-recipes-data" type="application/json">
                    @json($existingRecipes)
                </script>

                {{-- Elemen Root Alpine: Memanggil state recipeManager setelah data di-load --}}
                <div x-data="recipeManagerInit" x-init="start()" class="p-6 text-gray-900"> 
                    
                    <form action="{{ route('manager.menus.recipe.store', $menu) }}" method="POST">
                        @csrf

                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Bahan Baku yang Dibutuhkan</h3>
                        
                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                                Ada kesalahan saat memproses formulir.
                            </div>
                        @endif


                        {{-- Tabel Bahan Baku --}}
                        <div class="space-y-4">
                            <template x-for="(recipe, index) in recipes" :key="index">
                                <div class="flex items-center space-x-3">
                                    {{-- Input Bahan Baku ID --}}
                                    <div class="w-2/3">
                                        <label class="block text-sm font-medium text-gray-700">Bahan Baku</label>
                                        <select :name="'ingredients[' + index + '][bahan_baku_id]'" 
                                                x-model="recipe.bahan_baku_id" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                            <option value="" disabled>Pilih Bahan Baku</option>
                                            @foreach($bahanBakuList as $bahan)
                                                <option value="{{ $bahan->id }}">{{ $bahan->nama }} ({{ $bahan->unit }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Input Kuantitas --}}
                                    <div class="w-1/3">
                                        <label class="block text-sm font-medium text-gray-700">Kuantitas ({{ $menu->unit }})</label>
                                        <input type="number" 
                                               :name="'ingredients[' + index + '][kuantitas_digunakan]'" 
                                               x-model.number="recipe.kuantitas_digunakan" 
                                               min="0.01" step="0.01" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    </div>
                                    
                                    {{-- Tombol Hapus --}}
                                    <button type="button" @click="removeRecipe(index)" class="mt-5 text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10H8m8 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        {{-- Tombol Tambah Bahan --}}
                        <div class="mt-6 border-t pt-4">
                            <button type="button" @click="addRecipe" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition duration-150">
                                + Tambah Bahan
                            </button>
                        </div>

                        {{-- Tombol Simpan Resep (Elemen yang Hilang) --}}
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150">
                                Simpan Resep
                            </button>
                        </div>
                    </form>
                </div> {{-- Penutup x-data --}}
            </div>
        </div>
    </div>
    
    {{-- Script Global (Harus diluar x-data) --}}
    <script>
        // KUNCI PERBAIKAN: Gunakan fungsi global untuk inisialisasi yang stabil
        document.addEventListener('alpine:init', () => {
            Alpine.data('recipeManagerInit', () => ({
                recipes: [],
                
                start() {
                    let initialRecipes = [];
                    const jsonElement = document.getElementById('existing-recipes-data');
                    
                    if (jsonElement && jsonElement.textContent) {
                        try {
                            const existingRecipesJson = JSON.parse(jsonElement.textContent);
                            
                            if (Array.isArray(existingRecipesJson) && existingRecipesJson.length > 0) {
                                initialRecipes = existingRecipesJson.map(item => ({
                                    bahan_baku_id: item.id,
                                    kuantitas_digunakan: item.pivot ? (item.pivot.kuantitas_digunakan || 1) : 1
                                }));
                            }
                        } catch (e) {
                            console.error("Fatal Error: Gagal parsing JSON resep.", e);
                        }
                    }
                    
                    if (initialRecipes.length === 0) {
                        initialRecipes.push({ bahan_baku_id: '', kuantitas_digunakan: 1 });
                    }

                    // Set state recipes setelah parsing
                    this.recipes = initialRecipes; 
                },
                
                addRecipe() { 
                    this.recipes.push({ bahan_baku_id: '', kuantitas_digunakan: 1 });
                },

                removeRecipe(index) {
                    this.recipes.splice(index, 1);
                }
            }));
        });
    </script>
@endsection