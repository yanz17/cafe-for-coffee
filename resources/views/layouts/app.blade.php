<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'For Coffee POS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            {{-- HEADER HALAMAN: Mengganti logika $header lama dengan @yield --}}
            @hasSection('header')
                <header class="bg-white shadow-md border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main>
                {{-- KONTEN UTAMA --}}
                @yield('content')
            </main>
        </div>
        
        @livewireScripts
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Skenario 1: Non-Livewire Controller (Flash Session)
                    const invoiceUrl = "{{ session('print_invoice_url') }}"; 
                    if (invoiceUrl && invoiceUrl.length > 5) { 
                        window.open(invoiceUrl, '_blank'); 
                    }
                });
                
                // KOREKSI KRITIS: LISTENER GLOBAL UNTUK LIVEWIRE DISPATCH
                document.addEventListener('livewire:initialized', () => {
                    Livewire.on('open-invoice-tab', (orderId) => {
                        // Menerima ID (payload disederhanakan menjadi ID mentah)
                        const finalId = Array.isArray(orderId) ? parseInt(orderId[0]) : parseInt(orderId);
                        
                        if (finalId && !isNaN(finalId)) {
                            const invoiceUrl = `/kasir/order/${finalId}/invoice`;
                            window.open(invoiceUrl, '_blank'); 
                        } else {
                            console.error('Livewire Dispatch Gagal: ID tidak valid.', orderId);
                        }
                    });
                });
            </script>
        @stack('scripts')
    </body>
</html>