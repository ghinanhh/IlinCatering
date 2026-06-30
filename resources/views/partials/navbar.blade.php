<nav class="fixed w-full z-[100] glass border-b border-slate-200" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <span class="text-2xl font-bold text-orange-600">Ilin<span class="text-slate-800">Catering</span></span>
            </div>
            
            <div class="hidden md:flex items-center space-x-4 lg:space-x-6 text-sm lg:text-base font-medium">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600 transition">Beranda</a>
                <a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600 transition">Tentang Kami</a>
                <a href="{{ url('/#kategori') }}" class="text-slate-700 hover:text-orange-600 transition">Kategori</a>
                <a href="{{ route('cara_pemesanan') }}" class="{{ request()->routeIs('cara_pemesanan') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600 transition">Cara Pemesanan</a>
                <a href="{{ route('reviews') }}" class="{{ request()->routeIs('reviews') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600 transition">Review</a>
                <a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600 transition">Kontak</a>

                <div class="h-6 w-[1px] bg-slate-300 mx-1 lg:mx-2"></div> 

                @auth
                    <div class="flex items-center gap-4 lg:gap-5">
                        @if(Auth::user()->role === 'pelanggan')
                        <a href="{{ route('pelanggan.cart') }}" class="relative text-slate-600 hover:text-orange-600 transition">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                            
                            {{-- 🌟 REVISI FIX: Mengubah sum() menjadi count() agar menghitung banyak jenis menu, bukan total porsi --}}
                            @php
                                // Opsi A: Jika menggunakan tabel database Cart
                                $jumlahKeranjang = class_exists('\App\Models\Cart') ? \App\Models\Cart::where('user_id', auth()->id())->count() : 0;

                                // Opsi B: Jika menggunakan relasi Order items pending (aktifkan jika opsi A tidak sesuai)
                                // $jumlahKeranjang = \App\Models\Order::where('user_id', auth()->id())->where('status', 'pending')->first()?->items()->count() ?? 0;

                                // Opsi C: Jika menggunakan Session Laravel (aktifkan jika opsi A tidak sesuai)
                                // $jumlahKeranjang = session('cart') ? count(session('cart')) : 0;
                            @endphp

                            @if($jumlahKeranjang > 0)
                                <span class="absolute -top-2 -right-2 bg-orange-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">
                                    {{ $jumlahKeranjang }}
                                </span>
                            @else
                                <span class="absolute -top-2 -right-2 bg-slate-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">
                                    0
                                </span>
                            @endif
                        </a>
                        @endif

                        <a href="{{ route('dashboard') }}" class="bg-slate-100 text-slate-800 px-4 lg:px-5 py-2.5 rounded-full hover:bg-orange-100 hover:text-orange-600 transition font-bold flex items-center gap-2 border border-slate-200">
                            <i class="fa-solid fa-circle-user text-orange-600 text-lg"></i>
                            <span class="max-w-[80px] lg:max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                        </a>
                        
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition pt-1">
                                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-slate-700 hover:text-orange-600 transition font-bold">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-orange-600 text-white text-sm px-4 py-2 rounded-full hover:bg-orange-700 transition shadow-lg shadow-orange-200 font-bold tracking-wide">Daftar Akun</a>
                @endauth
            </div>

            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-slate-600 hover:text-orange-600 focus:outline-none p-2">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" @click.away="open = false" x-cloak 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        class="md:hidden absolute top-full left-0 w-full bg-white border-b border-slate-200 p-4 space-y-4 shadow-xl z-[100]">
        
        <a href="{{ url('/') }}" class="block font-medium px-2 py-1 {{ request()->is('/') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600">Beranda</a>
        <a href="{{ route('tentang') }}" class="block font-medium px-2 py-1 {{ request()->routeIs('tentang') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600">Tentang Kami</a>
        <a href="{{ url('/#kategori') }}" class="block font-medium px-2 py-1 text-slate-700 hover:text-orange-600">Kategori</a>
        <a href="{{ route('cara_pemesanan') }}" class="block font-medium px-2 py-1 {{ request()->routeIs('cara_pemesanan') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600">Cara Pemesanan</a>
        <a href="{{ route('reviews') }}" class="block font-medium px-2 py-1 {{ request()->routeIs('reviews') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600">Review</a>
        <a href="{{ route('kontak') }}" class="block font-medium px-2 py-1 {{ request()->routeIs('kontak') ? 'text-orange-600 font-bold' : 'text-slate-700' }} hover:text-orange-600">Kontak</a>
        
        <div class="border-t border-slate-100 pt-4 mt-2 px-2 flex flex-col gap-3">
            @auth
                <div class="text-sm text-slate-500 mb-2 text-center">Masuk sebagai: <span class="font-bold text-slate-800">{{ Auth::user()->name }}</span></div>
                <a href="{{ route('dashboard') }}" class="block w-full bg-slate-900 text-white text-center py-3 rounded-xl font-bold shadow-lg">Menu Dashboard</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="block w-full bg-red-50 text-red-600 text-center py-3 rounded-xl font-bold hover:bg-red-100 transition">
                        Keluar Akun
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block w-full bg-slate-100 text-slate-700 text-center py-3 rounded-xl font-bold">Masuk</a>
                <a href="{{ route('register') }}" class="block w-full bg-orange-600 text-white text-center py-3 rounded-xl font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200/50 tracking-wide">Daftar Akun</a>
            @endauth
        </div>
    </div>
</nav>