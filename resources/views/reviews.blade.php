<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pelanggan | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    <!-- 🌟 BERSIH & EFISIEN: Memanggil Master Navigasi Terpusat -->
    @include('partials.navbar')

    <!-- ─── SEKSI REVIEW UTAMA ─── -->
    <section class="pt-32 sm:pt-40 pb-16 sm:pb-24 px-4 bg-slate-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12 sm:mb-16">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Apa Kata <span class="text-orange-600">Pelanggan</span> Kami?</h1>
                <p class="text-slate-500 mt-4 max-w-2xl mx-auto text-base sm:text-lg">Testimoni jujur dari mereka yang telah menikmati sajian Ilin Catering.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
                
                @forelse($allReviews as $review)
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition duration-300 group flex flex-col justify-between">
                    
                    <div>
                        {{-- 📷 TINGGI FOTO PROPORSIOAL --}}
                        <div class="h-48 sm:h-52 overflow-hidden relative bg-slate-100 flex items-center justify-center">
                            @if($review->image)
                                <img src="{{ asset($review->image) }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                                    alt="Foto Review {{ $review->menu->title ?? 'Menu' }}">
                            @else
                                <img src="https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=600&q=80" 
                                    class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition duration-500" 
                                    alt="Default Review Image">
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            <div class="absolute bottom-3 left-4">
                                <span class="bg-white/90 backdrop-blur px-2.5 py-1 rounded-full text-[9px] font-black uppercase text-orange-600 shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-utensils"></i> {{ $review->menu->title ?? 'Menu Pilihan' }}
                                </span>
                            </div>
                        </div>

                        {{-- 📦 PADDING KONTEN PRESISI PAS CAKEP --}}
                        <div class="p-5 pb-0">
                            <div class="flex text-yellow-400 mb-2.5 text-[10px] gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>

                            <p class="text-slate-600 italic leading-relaxed text-sm">
                                "{!! nl2br(e($review->comment)) !!}"
                            </p>

                            {{-- 💬 KOTAK BALASAN ADMIN --}}
                            @if($review->admin_reply)
                                <div class="mt-3 p-3 bg-orange-50/60 border border-orange-100 rounded-2xl text-[11px] text-left">
                                    <p class="font-bold text-orange-800 flex items-center gap-1 mb-0.5">
                                        <i class="fa-solid fa-reply fa-flip-horizontal text-[9px]"></i> Balasan Ilin Catering:
                                    </p>
                                    <p class="text-slate-600 leading-relaxed font-medium">
                                        "{{ $review->admin_reply }}"
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 👤 BAGIAN PROFIL BAWAH RAPAT --}}
                    <div class="p-5 pt-3">
                        <div class="flex items-center gap-3 border-t border-slate-100 pt-3.5">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center font-black text-orange-600 text-[10px] shrink-0">
                                {{ strtoupper(substr($review->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-xs">{{ $review->user->name ?? 'Pelanggan Ilin' }}</p>
                                <p class="text-[8px] text-slate-400 uppercase tracking-widest font-bold">
                                    {{ $review->user_title ?? 'Pelanggan Terverifikasi' }}
                                </p>
                            </div>
                            <div class="ml-auto">
                                <p class="text-[8px] text-slate-300 uppercase font-bold">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                </div>
                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <i class="fa-solid fa-comments text-4xl"></i>
                    </div>
                    <p class="text-slate-400 italic font-medium">Belum ada review yang tersedia saat ini.</p>
                </div>
                @endforelse

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
                    <li><a href="{{ url('/#kategori') }}" class="text-slate-400 hover:text-orange-600 transition">Kategori</a></li>
                    <li><a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'text-orange-500 font-bold' : 'text-slate-400' }} hover:text-orange-600 transition">Tentang Kami</a></li>
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