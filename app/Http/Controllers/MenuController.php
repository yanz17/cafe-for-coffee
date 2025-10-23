<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('manager.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manager.menus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'kategori' => 'required|string',
        ]);

        Menu::create($request->all());

        return redirect()->route('manager.menus.index')
                        ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function showRecipeForm(Menu $menu)
    {
        // Ambil semua bahan baku yang tersedia untuk dropdown
        $bahanBakuList = BahanBaku::all();
        
        // Ambil resep yang sudah ada untuk menu ini
        $existingRecipes = $menu->bahanBaku()->get();
        
        return view('manager.menus.recipe', compact('menu', 'bahanBakuList', 'existingRecipes'));
    }

    public function storeRecipe(Request $request, Menu $menu)
    {
        $request->validate([
            'ingredients' => 'nullable|array',
            'ingredients.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'ingredients.*.kuantitas_digunakan' => 'required|integer|min:1',
        ]);

        // Sinkronisasi relasi many-to-many (attach/detach/update)
        $recipeData = [];
        if ($request->ingredients) {
            foreach ($request->ingredients as $item) {
                $recipeData[$item['bahan_baku_id']] = [
                    'kuantitas_digunakan' => $item['kuantitas_digunakan']
                ];
            }
        }

        // Gunakan sync() untuk memperbarui relasi pivot
        $menu->bahanBaku()->sync($recipeData);

        return redirect()->route('manager.menus.index')
                        ->with('success', 'Resep untuk menu ' . $menu->nama . ' berhasil diperbarui.');
    }
}
