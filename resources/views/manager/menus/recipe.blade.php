@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kelola Resep: {{ $menu->nama }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="recipeManager({!! $existingRecipes->toJson() !!})">
                    
                    <form action="{{ route('manager.menus.recipe.store', $menu) }}" method="POST">
                        @csrf

                        <h3 class="text-lg font-semibold mb-4">Daftar Bahan Baku yang Dibutuhkan</h3>

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
                                        <label class="block text-sm font-medium text-gray-700">Kuantitas</label>
                                        <input type="number" 
                                               :name="'ingredients[' + index + '][kuantitas_digunakan]'" 
                                               x-model.number="recipe.kuantitas_digunakan" 
                                               min="1" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    </div>
                                    
                                    {{-- Tombol Hapus --}}
                                    <button type="button" @click="removeRecipe(index)" class="mt-5 text-red-500 hover:text-red-700">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        <div class="mt-6 border-t pt-4">
                            <button type="button" @click="addRecipe" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                + Tambah Bahan
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Resep
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function recipeManager(existingRecipesJson) {
            let initialRecipes = existingRecipesJson.map(item => ({
                bahan_baku_id: item.id,
                kuantitas_digunakan: item.pivot.kuantitas_digunakan
            }));
            
            // Jika tidak ada resep, mulai dengan satu baris kosong
            if (initialRecipes.length === 0) {
                 initialRecipes.push({ bahan_baku_id: '', kuantitas_digunakan: 1 });
            }

            return {
                recipes: initialRecipes,

                addRecipe() {
                    this.recipes.push({ bahan_baku_id: '', kuantitas_digunakan: 1 });
                },

                removeRecipe(index) {
                    this.recipes.splice(index, 1);
                }
            }
        }
    </script>
@endsection