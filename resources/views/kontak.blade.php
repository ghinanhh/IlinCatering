<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    <!-- 🌟 MASTER NAVBAR TERPUSAT: Bersih, Ramping, dan Anti-Ribet -->
    @include('partials.navbar')

    <!-- ─── SEKSI KONTAK UTAMA ─── -->
    <section class="pt-32 sm:pt-40 pb-16 sm:pb-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-10 sm:mb-12 text-center">Hubungi <span class="text-orange-600">Kami</span></h1>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto items-stretch">
                
                {{-- SISI KIRI: INFORMASI KONTAK CARD --}}
                <div class="flex flex-col gap-6 justify-between">
                    <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 text-center transition-all hover:shadow-xl flex-1 flex flex-col justify-center">
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <h3 class="font-bold text-xl sm:text-2xl mb-2">WhatsApp</h3>
                        <p class="text-slate-500 mb-6 text-sm sm:text-base">Konsultasi menu dan pemesanan lebih cepat via chat.</p>
                        <a href="https://wa.me/62887435414960" target="_blank" class="inline-block w-full sm:w-auto bg-green-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-green-100 hover:bg-green-700 transition">
                            Chat Admin
                        </a>
                    </div>
                    
                    <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 text-center transition-all hover:shadow-xl flex-1 flex flex-col justify-center">
                        <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h3 class="font-bold text-xl sm:text-2xl mb-2">Lokasi</h3>
                        <p class="text-slate-500 mb-6 text-sm sm:text-base">Jalan Abdul Mutalib, Bati-Bati, Tanah Laut</p>
                        <div class="bg-slate-50 p-4 rounded-xl inline-block w-full sm:w-auto mx-auto">
                            <p class="font-bold text-slate-800 text-sm">Senin - Minggu</p>
                            <p class="text-orange-600 font-black">08.00 - 20.00 WITA</p>
                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: MAPS ASLI DENGAN INTEGRASI LINK EMBED MILIK KAMU --}}
                <div class="bg-white p-4 rounded-[2.5rem] shadow-sm border border-slate-100 min-h-[400px] lg:min-h-full transition-all hover:shadow-xl overflow-hidden flex">
                    <iframe 
                        class="w-full h-full rounded-[2rem] border-0 min-h-[350px]"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1087.2512093340072!2d114.70634318436711!3d-3.600938682094021!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de6917a64c81573%3A0x586bf6a27aeba0d8!2silin%20catering!5e0!3m2!1sid!2sid!4v1779974666890!5m2!1sid!2sid" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                
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
                    {{-- 🌟 SYNC: Menambahkan Link Menu Cara Pemesanan --}}
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