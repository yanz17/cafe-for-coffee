<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $categories = \App\Models\Menu::select('kategori')
                      ->whereNotNull('kategori') // Hapus kategori NULL
                      ->where('kategori', '!=', 'DUMMY_KATEGORI_BARU') // Hapus kategori dummy
                      ->distinct()
                      ->pluck('kategori')
                      ->sort();

        return view('manager.menus.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lakukan validasi terlebih dahulu (tetap wajib!)
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255|unique:menus,nama',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'kategori' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Siapkan data untuk penyimpanan
        $dataToStore = $request->except(['_token']); // Hapus field _token
        $dataToStore['foto'] = null; // Inisialisasi foto agar tidak ada path temporer yang tersimpan

        // Logika Upload Foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $folder = 'menu_photos';
            
            $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($folder, $file, $fileName);
            
            if ($path) {
                $dataToStore['foto'] = $path; // Simpan path yang BENAR
            } else {
                return back()->withInput()->with('error', 'Gagal menyimpan file. Cek izin folder storage/app/public.');
            }
        }

        // KOREKSI: Gunakan array yang sudah diolah ($dataToStore)
        $dataToStore = $request->except(['_token']);

        $safeData = array_merge($request->validated(), ['foto' => $dataToStore['foto']]);

        \App\Models\Menu::create($safeData);

        return redirect()->route('manager.menus.index')
                        ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu) // Menggunakan Route Model Binding
    {
        $categories = \App\Models\Menu::select('kategori')
                      ->whereNotNull('kategori')
                      ->where('kategori', '!=', 'DUMMY_KATEGORI_BARU')
                      ->distinct()
                      ->pluck('kategori')
                      ->sort();

        return view('manager.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:menus,nama,' . $menu->id,
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'kategori' => 'required|string',
            'is_tersedia' => 'nullable|boolean', // Gunakan nullable untuk keamanan
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ambil semua data request, KECUALI field internal Laravel
        $dataToUpdate = $request->except(['_token', '_method']);

        // Logika Upload/Update Foto
        if ($request->hasFile('foto')) {
            // 1. Hapus foto lama
            if ($menu->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->foto);
            }
            
            // 2. Simpan foto baru
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('menu_photos', $file, $fileName);
            
            if ($path) {
                $dataToUpdate['foto'] = $path;
            } else {
                return back()->withInput()->with('error', 'Gagal memindahkan file foto.');
            }
        } else {
            // Pertahankan foto lama jika tidak ada upload baru
            $dataToUpdate['foto'] = $menu->foto; 
        }
        
        // Perbaikan: Pastikan is_tersedia diolah dari checkbox
        if (!$request->has('is_tersedia')) {
            $dataToUpdate['is_tersedia'] = 0;
        }
        
        $menu->update($dataToUpdate);

        return redirect()->route('manager.menus.index')->with('success', 'Menu ' . $menu->nama . ' berhasil diperbarui.');
    }

    /**
     * Display the specified resource.
     */
    public function showRecipeForm(Menu $menu)
    {
        // EAGER LOAD RELASI: Gunakan load() agar data pivot terambil
        $menu->load('bahanBaku');

        // Ambil semua bahan baku yang tersedia untuk dropdown
        $bahanBakuList = \App\Models\BahanBaku::all();
        
        // Ambil resep yang sudah ada untuk menu ini
        $existingRecipes = $menu->bahanBaku;
        
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

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Menu $menu)
    {
        // CATATAN PENTING:
        // Menghapus menu akan memicu penghapusan otomatis (CASCADE)
        // semua order_items dan menu_bahan_bakus yang terkait.
        
        $menu->delete();

        return redirect()->route('manager.menus.index')
                        ->with('success', 'Menu ' . $menu->nama . ' berhasil dihapus.');
    }

    /**
     * Menampilkan dan mengelola daftar kategori menu yang unik (READ).
     */
    public function categoriesIndex()
    {
        // Mengambil semua kategori unik yang saat ini ada di database
        $categories = Menu::select('kategori')
            ->distinct()
            ->pluck('kategori'); // Mengambil nama kategori sebagai array
            
        return view('manager.menus.categories.index', compact('categories'));
    }

    /**
     * Menambahkan kategori baru (CREATE).
     */
    public function categoryStore(Request $request)
    {
        $request->validate([
            // Validasi masih menggunakan tabel menus
            'new_category_name' => 'required|string|max:50|unique:menus,kategori',
        ], [
            'new_category_name.unique' => 'Kategori ini sudah ada.',
            'new_category_name.required' => 'Nama kategori wajib diisi.',
        ]);

        // KUNCI PERBAIKAN: Membuat entri Menu dummy untuk menyimpan nama kategori.
        // Menu ini akan disetel menjadi is_hidden = true (jika kolom ada) atau
        // diberi nama yang jelas-jelas tidak valid/dummy.
        
        Menu::create([
            'nama' => 'DUMMY_KATEGORI_BARU', // Nama dummy
            'kategori' => $request->new_category_name, // Nama kategori yang ingin disimpan
            'harga' => 0, // Harga 0
            'stok' => 0,  // Stok 0
            // Tambahkan kolom lain yang required di tabel menus Anda jika ada
        ]);

        return redirect()->route('manager.categories.index')
            ->with('success', "Kategori '{$request->new_category_name}' berhasil ditambahkan dan siap digunakan.");
    }

    /**
     * Mengganti nama kategori yang sudah ada (UPDATE).
     */
    public function categoryUpdate(Request $request)
    {
        $request->validate([
            'old_category_name' => 'required|string|exists:menus,kategori',
            'new_category_name' => 'required|string|max:50|unique:menus,kategori',
        ]);
        
        // KUNCI: Mengganti nama kategori di SEMUA menu yang terkait
        Menu::where('kategori', $request->old_category_name)->update([
            'kategori' => $request->new_category_name
        ]);

        return redirect()->route('manager.menus.categories.index')
            ->with('success', "Kategori '{$request->old_category_name}' berhasil diubah menjadi '{$request->new_category_name}'.");
    }

    /**
     * Menghapus kategori (DELETE).
     */
    public function categoryDestroy(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|exists:menus,kategori',
        ]);

        // KUNCI: Menghapus kategori berarti men-set field 'kategori' di menu menjadi NULL (atau default)
        // Pilihan Aman: Set kategori menjadi NULL atau 'Uncategorized'
        Menu::where('kategori', $request->category_name)->update([
            'kategori' => 'Uncategorized' 
        ]);

        return redirect()->route('manager.menus.categories.index')
            ->with('success', "Kategori '{$request->category_name}' berhasil dihapus. Menu yang terkait disetel ke 'Uncategorized'.");
    }
}
