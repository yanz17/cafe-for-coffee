{{-- resources/views/layouts/guest.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- ... meta tags ... --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center py-10 bg-gray-50">
            
            {{-- KOREKSI UTAMA: Div Logo dan Nama Brand --}}
            <div class="mb-8 flex flex-col items-center justify-center"> {{-- Menambah flex-col items-center --}}
                <a href="/" class="flex flex-col items-center"> {{-- Menambah flex-col items-center --}}
                    {{-- Mengubah ukuran logo: w-24 h-24 atau w-32 h-32 untuk lebih besar --}}
                    <x-application-logo class="w-28 h-28 fill-current text-indigo-600 mb-2" /> 
                    <h1 class="text-3xl font-extrabold text-gray-800 text-center">For Coffee POS</h1> 
                </a>
            </div>

            {{-- Card Formulir --}}
            <div class="w-full sm:max-w-md px-8 py-6 bg-white shadow-2xl rounded-xl border border-gray-200">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>