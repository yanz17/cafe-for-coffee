<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    protected function redirectTo()
    {
        $role = Auth::user()?->role ?? null;

        return match ($role) {
            'manager' => route('manager.dashboard'),
            'kasir' => route('kasir.pos'),
            'pelanggan' => route('customer.menu'), 
            default => '/',
        };
    }

    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // 1. Dapatkan role pengguna yang baru saja login
        $role = Auth::user()?->role ?? null;

        // 2. Tentukan tujuan redirect berdasarkan role (menggunakan logika dari redirectTo() Anda)
        $redirectUrl = match ($role) {
            'manager' => route('manager.dashboard'),
            'kasir' => route('kasir.pos'),
            'pelanggan' => route('customer.menu'), 
            default => route('dashboard'), // Fallback ke route dashboard jika role tidak teridentifikasi
        };

        // 3. Lakukan redirect ke URL yang sudah ditentukan
        // Kita hapus intended() dan langsung redirect untuk mengamankan flow POS
        return redirect($redirectUrl); 
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
