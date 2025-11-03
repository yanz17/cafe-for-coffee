<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-2xl font-extrabold text-gray-900 mb-2 text-center">Masuk ke Akun</h2>
    <p class="text-sm text-gray-500 mb-6 text-center border-b pb-3">Akses Cepat untuk Staff & Pelanggan</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email Address --}}
        <div class="mb-4 px-5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mb-4 px-5">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="flex justify-between items-center text-sm mb-6 px-5"> 
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="underline text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="flex flex-col space-y-3 mt-4 pt-3 border-t px-5">
            
            {{-- Tombol Login Utama --}}
            <button type="submit" 
                    class="w-full 
                        bg-indigo-600 hover:bg-indigo-700 
                        text-white font-semibold 
                        py-3 rounded-lg 
                        transition duration-150 
                        focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500 focus:outline-none 
                        shadow-md">
                {{ __('Log in') }}
            </button>
            
            {{-- Link Register di bawah --}}
            @if (Route::has('register'))
                <a class="w-full text-center text-sm text-indigo-600 hover:text-indigo-800 underline" href="{{ route('register') }}">
                    {{ __('Belum punya akun? Register') }}
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>