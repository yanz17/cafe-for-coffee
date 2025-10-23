<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource (Menampilkan daftar staf: Kasir dan Manajer).
     */
    public function index()
    {
        // Filter hanya menampilkan staf, bukan pelanggan yang register sendiri
        $users = User::whereIn('role', [User::ROLE_KASIR, User::ROLE_MANAGER])
                     ->orderBy('role', 'desc')
                     ->orderBy('name', 'asc')
                     ->get();
                     
        return view('manager.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource (Menampilkan form tambah).
     */
    public function create()
    {
        return view('manager.users.create');
    }

    /**
     * Store a newly created resource in storage (Menyimpan akun staf baru).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            // Hanya izinkan Manajer membuat role Kasir atau Manajer
            'role' => ['required', Rule::in([User::ROLE_KASIR, User::ROLE_MANAGER])], 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('manager.users.index')->with('success', 'Akun staf berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource (Menampilkan form edit).
     */
    public function edit(User $user)
    {
        // Pastikan user yang diedit bukan pelanggan
        if ($user->role === User::ROLE_PELANGGAN) {
            return redirect()->route('manager.users.index')->with('error', 'Tidak dapat mengedit akun pelanggan.');
        }
        return view('manager.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage (Mengupdate data staf).
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => ['required', Rule::in([User::ROLE_KASIR, User::ROLE_MANAGER])],
            // Password baru bersifat opsional (nullable)
            'password' => 'nullable|string|min:8|confirmed', 
        ]);

        // Tangani update password hanya jika ada input
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            // Hapus field password dari array data jika kosong, agar tidak menimpa password lama
            unset($data['password']); 
        }

        $user->update($data);

        return redirect()->route('manager.users.index')->with('success', 'Akun ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (Menghapus akun staf).
     */
    public function destroy(User $user)
    {
        // Pencegahan: Jangan biarkan manajer menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }
        
        // Pastikan yang dihapus bukan pelanggan
        if ($user->role === User::ROLE_PELANGGAN) {
            return back()->with('error', 'Tidak dapat menghapus akun pelanggan dari sini.');
        }

        $user->delete();

        return redirect()->route('manager.users.index')->with('success', 'Akun ' . $user->name . ' berhasil dihapus.');
    }
}