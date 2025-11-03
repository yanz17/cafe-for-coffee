<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo & Brand Name -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('manager.dashboard') ?? route('customer.menu') }}" class="flex items-center space-x-2"> 
                        
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600" />
                        <span class="font-extrabold text-indigo-600 text-lg">For Coffee POS</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- 1. Tautan untuk ROLE MANAJER --}}
                    @if (Auth::user()->role === \App\Models\User::ROLE_MANAGER)
                        <x-nav-link :href="route('manager.dashboard')" :active="request()->routeIs('manager.dashboard')">
                            {{ __('Dashboard Manajer') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.menus.index')" :active="request()->routeIs('manager.menus.*')">
                            {{ __('Menu & Resep') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.categories.index')" :active="request()->routeIs('manager.categories.*')">
                            {{ __('Kelola Kategori') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.bahan_baku.index')" :active="request()->routeIs('manager.bahan_baku.*')">
                            {{ __('Inventaris') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.users.index')" :active="request()->routeIs('manager.users.*')">
                            {{ __('Kelola Staff') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.reports.sales')" :active="request()->routeIs('manager.reports.*')">
                            {{ __('Laporan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('manager.feedbacks.index')" :active="request()->routeIs('manager.feedbacks.index')">
                            {{ __('Feedback') }}
                        </x-nav-link>
                    @endif

                    {{-- 2. Tautan untuk ROLE KASIR --}}
                    @if (Auth::user()->role === \App\Models\User::ROLE_KASIR)
                        <x-nav-link :href="route('kasir.pos')" :active="request()->routeIs('kasir.pos')">
                            {{ __('POS System') }}
                        </x-nav-link>
                        <x-nav-link :href="route('kasir.orders.index')" :active="request()->routeIs('kasir.orders.index')">
                            {{ __('Daftar Pesanan') }}
                        </x-nav-link>
                    @endif

                    {{-- 3. Tautan untuk ROLE PELANGGAN --}}
                    @if (Auth::user()->role === \App\Models\User::ROLE_PELANGGAN)
                        <x-nav-link :href="route('customer.menu')" :active="request()->routeIs('customer.menu')">
                            {{ __('Pesan Menu') }}
                        </x-nav-link>
                        <x-nav-link :href="route('customer.orders')" :active="request()->routeIs('customer.orders')">
                            {{ __('Pesanan Saya') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown (Tetap, tapi dark mode class dihapus) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- ... (Profile dan Logout Links) ... -->
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <x-dropdown-link :href="route('logout')" 
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            
            <!-- ... (Hamburger dan Responsive Nav di bagian bawah) ... -->
        </div>
    </div>

    <!-- ... (Responsive Navigation Menu - hapus dark mode class di sini juga) ... -->
</nav>