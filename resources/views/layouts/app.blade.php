<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Ilin Catering</title>
    
    {{-- 🌟 PENYEMPURNAAN: Menyisipkan token CSRF global di Head agar sesi lebih stabil --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans" x-data="{ sidebarOpen: window.innerWidth >= 768 }" @resize.window="sidebarOpen = window.innerWidth >= 768">

    <div class="md:hidden bg-slate-900 text-white p-4 flex items-center justify-between sticky top-0 z-30 shadow-md no-print">
        <span class="text-xl font-bold text-orange-500">Ilin<span class="text-white">Catering</span></span>
        <button @click="sidebarOpen = true" class="text-white hover:text-orange-500 focus:outline-none">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 md:hidden no-print" x-cloak></div>

    <aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0 md:w-20'" 
           class="fixed left-0 top-0 h-screen bg-slate-900 text-white transition-all duration-300 z-50 overflow-y-auto overflow-x-hidden no-print shadow-2xl md:shadow-none">
        
        <div class="p-6 flex items-center justify-between">
            <span x-show="sidebarOpen" class="text-xl font-bold text-orange-500">Ilin<span class="text-white">Catering</span></span>
            
            <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-orange-500 focus:outline-none hidden md:block">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            
            <button @click="sidebarOpen = false" class="text-white hover:text-orange-500 focus:outline-none md:hidden">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        @php
            $role = Auth::user()->role ?? 'pelanggan'; 
        @endphp

        <nav class="mt-6 px-4 space-y-2 pb-10">
            
            {{-- Menu Beranda Umum --}}
            <a href="{{ route('dashboard') }}" 
               class="{{ (request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('owner.dashboard') || request()->routeIs('pelanggan.dashboard')) ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                <i class="fa-solid fa-house w-6 text-center"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Beranda</span>
            </a>

            {{-- Menu Khusus Pelanggan --}}
            @if($role == 'pelanggan')
                <div class="pt-4 pb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap" x-show="sidebarOpen">Menu Pesanan</div>
                
                <a href="{{ route('pelanggan.menu') }}" class="{{ request()->routeIs('pelanggan.menu') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-utensils w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Pilih Menu</span>
                </a>

                <a href="{{ route('pelanggan.cart') }}" class="{{ request()->routeIs('pelanggan.cart') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-cart-shopping w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Keranjang</span>
                </a>

                <a href="{{ route('pelanggan.riwayat') }}" class="{{ request()->routeIs('pelanggan.riwayat') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-clock-rotate-left w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Riwayat Pesanan</span>
                </a>
            @endif 

            {{-- Menu Khusus Admin --}}
            @if($role == 'admin')
                <div class="pt-4 pb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap" x-show="sidebarOpen">Manajemen Data</div>
                
                <a href="{{ route('admin.menu') }}" class="{{ request()->routeIs('admin.menu') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-utensils w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Kelola Menu</span>
                </a>

                <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-list-check w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Kelola Pesanan</span>
                    @if(isset($newOrdersCount) && $newOrdersCount > 0)
                        <span x-show="sidebarOpen" class="ml-auto px-2 py-0.5 text-[10px] font-black bg-rose-500 text-white rounded-full animate-pulse shadow-sm shadow-rose-900/50">
                            {{ $newOrdersCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.orders.archive') }}" class="{{ request()->routeIs('admin.orders.archive') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-box-archive w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Arsip Pesanan</span>
                </a>

                <div class="pt-4 pb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap" x-show="sidebarOpen">Laporan & Monitoring</div>
                
                <a href="{{ route('admin.report') }}" class="{{ request()->routeIs('admin.report') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Laporan Penjualan</span>
                </a>

                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.index') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-star-half-stroke w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Manajemen Review</span>
                </a>
            @endif

            {{-- Menu Khusus Owner --}}
            @if($role == 'owner')
                <div class="pt-4 pb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap" x-show="sidebarOpen">Laporan & Monitoring</div>
                
                <a href="{{ route('owner.report') }}" class="{{ request()->routeIs('owner.report') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Laporan Penjualan</span>
                </a>

                {{-- 🌟 GERBANG LINK ARSIP UNTUK OWNER --}}
                <a href="{{ route('owner.orders.archive') }}" class="{{ request()->routeIs('owner.orders.archive') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-box-archive w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Arsip Pesanan</span>
                </a>

                <a href="{{ route('owner.reviews') }}" class="{{ request()->routeIs('owner.reviews') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-4 p-3 rounded-lg transition">
                    <i class="fa-solid fa-star w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Semua Review</span>
                </a>
            @endif

            <div class="pt-8 mt-4 border-t border-slate-800">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="flex items-center gap-4 p-3 rounded-lg hover:bg-red-500/10 text-red-400 transition">
                    <i class="fa-solid fa-right-from-bracket w-6 text-center"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Keluar</span>
                </a>
            </div>
        </nav>
    </aside>

    <main :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'" class="transition-all duration-300 p-4 sm:p-8">
        @yield('content')
    </main>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Mencegah sidebar tercetak saat print laporan */
        @media print {
            .no-print { display: none !important; }
            main { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</body>
</html>