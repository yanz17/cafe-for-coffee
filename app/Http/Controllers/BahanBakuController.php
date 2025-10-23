<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource (Menampilkan daftar inventaris).
     */
    public function index()
    {
        $bahanBakus = BahanBaku::orderBy('nama', 'asc')->get();
        // Pastikan nama variabel di sini cocok dengan yang dipakai di view: $bahanBakus
        return view('manager.bahan_baku.index', compact('bahanBakus'));
    }

    /**
     * Show the form for creating a new resource (Menampilkan form tambah).
     */
    public function create()
    {
        return view('manager.bahan_baku.create');
    }

    /**
     * Store a newly created resource in storage (Menyimpan data baru).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:bahan_bakus,nama',
            'unit' => 'required|string|max:50',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
        ]);

        BahanBaku::create($request->all());

        return redirect()->route('manager.bahan_baku.index')
                         ->with('success', 'Bahan baku ' . $request->nama . ' berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource (Menampilkan form edit).
     */
    public function edit(BahanBaku $bahanBaku)
    {
        return view('manager.bahan_baku.edit', compact('bahanBaku'));
    }

    /**
     * Update the specified resource in storage (Mengupdate data atau Restock).
     */
    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:bahan_bakus,nama,' . $bahanBaku->id,
            'unit' => 'required|string|max:50',
            'stok_saat_ini' => 'required|integer|min:0', // Ini juga untuk fungsi Restock
            'stok_minimal' => 'required|integer|min:0',
        ]);

        $bahanBaku->update($request->all());

        return redirect()->route('manager.bahan_baku.index')
                         ->with('success', 'Bahan baku ' . $bahanBaku->nama . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (Menghapus data).
     */
    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();

        return redirect()->route('manager.bahan_baku.index')
                         ->with('success', 'Bahan baku ' . $bahanBaku->nama . ' berhasil dihapus.');
    }
}