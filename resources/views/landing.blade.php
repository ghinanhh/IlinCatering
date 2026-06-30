<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ilin Catering | Rasa Rumahan, Kualitas Restoran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .service-card:hover { transform: translateY(-10px); }
        .menu-card:hover img { transform: scale(1.1); }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900" x-data="{ category: 'box' }">

    <!-- 🌟 SEKARANG LEBIH RINGKAS: Memanggil Master Navigasi Terpusat -->
    @include('partials.navbar')

    <!-- ─── HERO SECTION ─── -->
    <section class="pt-32 pb-20 px-4">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 text-center lg:text-left">
            <div class="flex-1 space-y-6" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 -translate-x-10">
                    <span class="inline-block bg-orange-100 text-orange-700 px-4 py-1 rounded-full text-xs sm:text-sm font-semibold uppercase tracking-wider">Higienis & Halal</span>
                    <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-tight mt-4">
                        Sajian Lezat untuk <br class="hidden lg:block"><span class="text-orange-600">Momen Spesial</span> Anda.
                    </h1>
                    <p class="text-base sm:text-lg text-slate-600 mt-6 max-w-lg mx-auto lg:mx-0">
                        Ilin Catering menyediakan berbagai pilihan menu sehat, enak, dan terjangkau untuk acara kantor hingga kebutuhan harian.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 mt-8">
                        <a href="{{ Auth::check() ? route('pelanggan.menu') : '#kategori' }}" 
                        class="px-8 py-4 bg-orange-600 text-white rounded-2xl hover:bg-orange-700 transition shadow-xl shadow-orange-200 font-bold flex items-center justify-center gap-2">
                            <i class="fa-solid fa-utensils"></i>
                            {{ Auth::check() ? 'Pesan Sekarang' : 'Lihat Menu' }}
                        </a>
                        
                        <a href="{{ route('tentang') }}" class="px-8 py-4 border-2 border-slate-200 rounded-2xl hover:bg-slate-100 transition flex items-center justify-center gap-2 font-bold text-slate-700">
                            <i class="fa-regular fa-circle-question text-orange-600"></i> Tentang Kami
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 relative w-full mt-8 lg:mt-0" x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
                <div x-show="show" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 scale-90" class="relative z-10 flex justify-center">
                    <img src="https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Catering Food" class="w-full max-w-md lg:max-w-full object-cover rounded-[2.5rem] shadow-2xl border-8 border-white">
                </div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[100%] h-[100%] lg:w-[120%] lg:h-[120%] bg-orange-200/40 rounded-full blur-3xl -z-0"></div>
            </div>
        </div>
    </section>

    <!-- ─── LAYANAN SISI KATERING ─── -->
    <section id="layanan" class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold italic text-orange-600">Pilihan Layanan</h2>
            <p class="text-3xl sm:text-4xl font-extrabold mt-2 text-slate-900">Solusi Katering Lengkap</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mt-12 sm:mt-16">
                <div class="service-card p-6 sm:p-8 rounded-3xl bg-slate-50 border border-slate-100 transition duration-300 group">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mx-auto group-hover:bg-orange-600 group-hover:text-white transition">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mt-6">Nasi Kotak</h3>
                    <p class="text-slate-500 mt-4 text-sm leading-relaxed">Menu praktis untuk seminar, rapat, atau acara syukuran dengan kemasan eksklusif.</p>
                </div>

                <div class="service-card p-6 sm:p-8 rounded-3xl bg-slate-50 border border-slate-100 transition duration-300 group">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mx-auto group-hover:bg-orange-600 group-hover:text-white transition">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mt-6">Prasmanan</h3>
                    <p class="text-slate-500 mt-4 text-sm leading-relaxed">Sajian prasmanan elegan untuk berbagai acara dengan dekorasi cantik.</p>
                </div>

                <div class="service-card p-6 sm:p-8 rounded-3xl bg-slate-50 border border-slate-100 transition duration-300 group sm:col-span-2 lg:col-span-1">
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mx-auto group-hover:bg-orange-600 group-hover:text-white transition">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold mt-6">Harian/Kantor</h3>
                    <p class="text-slate-500 mt-4 text-sm leading-relaxed">Langganan makan siang kantor dengan menu yang berganti setiap harinya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── KATEGORI ETALASE MENU ─── -->
    <section id="kategori" class="py-16 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-10 sm:mb-12">
                <h2 class="text-orange-600 font-bold italic text-lg sm:text-xl">Pilih Sesuai Kebutuhan</h2>
                <p class="text-3xl sm:text-4xl font-extrabold mt-2">Kategori Menu</p>
            </div>

            <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mb-12 sm:mb-16">
                <button @click="category = 'box'" :class="category === 'box' ? 'bg-orange-600 text-white shadow-lg' : 'bg-white text-slate-600'" class="px-6 sm:px-8 py-2 sm:py-3 rounded-xl text-sm sm:text-base font-bold transition duration-300 w-[45%] sm:w-auto">Nasi Box</button>
                <button @click="category = 'prasmanan'" :class="category === 'prasmanan' ? 'bg-orange-600 text-white shadow-lg' : 'bg-white text-slate-600'" class="px-6 sm:px-8 py-2 sm:py-3 rounded-xl text-sm sm:text-base font-bold transition duration-300 w-[45%] sm:w-auto">Prasmanan</button>
                <button @click="category = 'snack'" :class="category === 'snack' ? 'bg-orange-600 text-white shadow-lg' : 'bg-white text-slate-600'" class="px-6 sm:px-8 py-2 sm:py-3 rounded-xl text-sm sm:text-base font-bold transition duration-300 w-[45%] sm:w-auto mt-2 sm:mt-0">Snack Box</button>
            </div>

            <div class="mt-8"> 
                <template x-if="category === 'box'">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        @forelse($boxMenus as $box)
                            <div class="bg-white rounded-[2.5rem] p-5 sm:p-6 shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                                <div>
                                    <div class="overflow-hidden rounded-[2rem] h-56 sm:h-64 mb-6">
                                        <img src="{{ asset('storage/' . $box->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 px-2">{{ $box->title }}</h3>
                                    <p class="text-slate-500 mt-3 px-2 text-xs sm:text-sm leading-relaxed line-clamp-2">
                                        {{ $box->description }}
                                    </p>
                                </div>
                                <div class="mt-6 px-2 pb-2">
                                    <p class="text-orange-600 font-extrabold text-xl sm:text-2xl">Rp {{ number_format($box->price, 0, ',', '.') }}</p>
                                    <a href="{{ Auth::check() ? route('pelanggan.menu') : route('login') }}" class="mt-4 w-full bg-slate-900 text-white text-center py-3 rounded-2xl font-bold hover:bg-orange-600 transition block">
                                        Pesan Sekarang
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10 text-slate-400 italic">Belum ada menu untuk kategori ini.</div>
                        @endforelse
                    </div>
                </template>

                <template x-if="category === 'prasmanan'">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        @forelse($prasmananMenus as $prasmanan)
                            <div class="bg-white rounded-[2.5rem] p-5 sm:p-6 shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                                <div>
                                    <div class="overflow-hidden rounded-[2rem] h-56 sm:h-64 mb-6">
                                        <img src="{{ asset('storage/' . $prasmanan->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 px-2">{{ $prasmanan->title }}</h3>
                                    <p class="text-slate-500 mt-3 px-2 text-xs sm:text-sm leading-relaxed line-clamp-2">
                                        {{ $prasmanan->description }}
                                    </p>
                                </div>
                                <div class="mt-6 px-2 pb-2">
                                    <p class="text-orange-600 font-extrabold text-xl sm:text-2xl">Rp {{ number_format($prasmanan->price, 0, ',', '.') }}</p>
                                    <a href="{{ Auth::check() ? route('pelanggan.menu') : route('login') }}" class="mt-4 w-full bg-slate-900 text-white text-center py-3 rounded-2xl font-bold hover:bg-orange-600 transition block">
                                        Pesan Sekarang
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10 text-slate-400 italic">Belum ada menu untuk kategori ini.</div>
                        @endforelse
                    </div>
                </template>

                <template x-if="category === 'snack'">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        @forelse($snackMenus as $snack)
                            <div class="bg-white rounded-[2.5rem] p-5 sm:p-6 shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                                <div>
                                    <div class="overflow-hidden rounded-[2rem] h-56 sm:h-64 mb-6">
                                        <img src="{{ asset('storage/' . $snack->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 px-2">{{ $snack->title }}</h3>
                                    <p class="text-slate-500 mt-3 px-2 text-xs sm:text-sm leading-relaxed line-clamp-2">
                                        {{ $snack->description }}
                                    </p>
                                </div>
                                <div class="mt-6 px-2 pb-2">
                                    <p class="text-orange-600 font-extrabold text-xl sm:text-2xl">Rp {{ number_format($snack->price, 0, ',', '.') }}</p>
                                    <a href="{{ Auth::check() ? route('pelanggan.menu') : route('login') }}" class="mt-4 w-full bg-slate-900 text-white text-center py-3 rounded-2xl font-bold hover:bg-orange-600 transition block">
                                        Pesan Sekarang
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10 text-slate-400 italic">Belum ada menu untuk kategori ini.</div>
                        @endforelse
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- ─── MENU FAVORIT ILIN ─── -->
    <section id="favorit" class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 sm:mb-12 gap-4">
                <div class="text-center md:text-left w-full md:w-auto">
                    <h2 class="text-orange-600 font-bold italic text-lg sm:text-xl">Paling Banyak Dipesan</h2>
                    <p class="text-3xl sm:text-4xl font-extrabold mt-2 text-slate-900">Menu Favorit Ilin</p>
                </div>
                <a href="{{ Auth::check() ? route('pelanggan.menu') : route('login') }}" class="text-orange-600 font-bold flex items-center justify-center w-full md:w-auto gap-2 hover:gap-4 transition-all">
                    Lihat Semua Menu <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($menus as $item)
                <div class="bg-slate-50 p-4 rounded-[2rem] shadow-sm hover:shadow-xl transition-all border border-slate-100 group menu-card">
                    <div class="relative overflow-hidden rounded-[1.5rem] h-48 sm:h-52 mb-4">
                        <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover transition duration-500">
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-orange-600 shadow-sm">
                            ⭐ 5.0
                        </div>
                    </div>
                    <h4 class="font-bold text-lg px-2 text-slate-900">{{ $item->title }}</h4>
                    <p class="text-slate-500 text-xs px-2 mt-1 truncate">{{ $item->description }}</p>
                    <div class="flex justify-between items-center mt-4 px-2 pb-2">
                        <span class="text-orange-600 font-extrabold text-lg">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        
                        <a href="{{ Auth::check() ? route('pelanggan.menu') : route('login') }}" 
                        class="w-10 h-10 bg-slate-900 text-white rounded-full hover:bg-orange-600 transition flex items-center justify-center">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ─── FOOTER UTAMA ─── -->
    <footer class="bg-slate-900 text-white py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 sm:gap-12 border-b border-slate-800 pb-10 sm:pb-12 text-center sm:text-left">
            <div>
                <h3 class="text-2xl font-bold text-orange-500 mb-4 sm:mb-6 uppercase tracking-wider">Ilin Catering</h3>
                <p class="text-slate-400 leading-relaxed text-sm sm:text-base">Kami berdedikasi memberikan pengalaman makan terbaik untuk setiap acara Anda di area Bati-Bati dan sekitarnya.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4 sm:mb-6 text-lg">Navigasi Cepat</h4>
                <ul class="space-y-3 sm:space-y-4 text-sm sm:text-base">
                    <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-orange-500 font-bold' : 'text-slate-400' }} hover:text-orange-600 transition">Beranda</a></li>
                    <li><a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'text-orange-500 font-bold' : 'text-slate-400' }} hover:text-orange-600 transition">Tentang Kami</a></li>
                    <li><a href="{{ url('/#kategori') }}" class="text-slate-400 hover:text-orange-600 transition">Kategori</a></li>
                    <li><a href="{{ route('cara_pemesanan') }}" class="{{ request()->routeIs('cara_pemesanan') ? 'text-orange-500 font-bold' : 'text-slate-400' }} hover:text-orange-600 transition">Cara Pemesanan</a></li>
                    <li><a href="{{ route('reviews') }}" class="{{ request()->routeIs('reviews') ? 'text-orange-500 font-bold' : 'text-slate-400' }} hover:text-orange-600 transition">Review</a></li>
                    <li><a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'text-orange-500 font-bold' : 'text-slate-400' }} hover:text-orange-600 transition">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 sm:mb-6 text-lg">Kontak Kami</h4>
                <div class="space-y-3 sm:space-y-4 text-slate-400 text-sm sm:text-base">
                    <p class="flex items-center justify-center sm:justify-start"><i class="fa-brands fa-whatsapp text-orange-500 w-6"></i> +62 887435414960</p>
                    <p class="flex items-center justify-center sm:justify-start"><i class="fa-solid fa-location-dot text-orange-500 w-6"></i> Bati-Bati, Tanah Laut</p>
                    <p class="flex items-center justify-center sm:justify-start"><i class="fa-regular fa-clock text-orange-500 w-6"></i> Senin – Minggu | 08.00 – 20.00</p>
                </div>
            </div>
        </div>
        <div class="text-center pt-6 sm:pt-8 text-xs sm:text-sm text-slate-500">
            &copy; 2026 Ilin Catering. Made with ♡ by Ghina.
        </div>
    </footer>

</body>
</html>