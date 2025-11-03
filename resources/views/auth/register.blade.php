{{-- resources/views/auth/register.blade.php --}}

<x-guest-layout>
    <h2 class="text-2xl font-extrabold text-gray-900 mb-2 text-center">Buat Akun Pelanggan</h2>
    <p class="text-sm text-gray-500 mb-6 text-center border-b pb-3">Daftar untuk memesan dari HP Anda.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-4 px-5">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email Address --}}
        <div class="mb-4 px-5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mb-4 px-5">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mb-6 px-5">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col space-y-3 mt-4 pt-3 border-t px-5">
            {{-- Tombol Register --}}
            <button type="submit" 
                    class="w-full 
                        bg-indigo-600 hover:bg-indigo-700 
                        text-white font-bold 
                        py-3 rounded-xl  transition duration-200 
                        ring-2 ring-indigo-400 focus:ring-4 focus:ring-offset-2 focus:ring-indigo-600 focus:outline-none 
                        shadow-lg hover:shadow-xl"> {{ __('Register') }}
            </button>

            {{-- Link Login --}}
            <a class="w-full text-center text-sm text-gray-600 hover:text-gray-800 underline" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Masuk') }}
            </a>
        </div>
    </form>
</x-guest-layout>