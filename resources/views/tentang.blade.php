<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | Ilin Catering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    @include('partials.navbar')

    <section class="pt-32 sm:pt-40 pb-16 sm:pb-20 px-4">
        {{-- 🌟 FIX MAKSIMAL: Kontainer diperlebar penuh ke max-w-7xl sejajar dengan footer agar tidak bantet --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-8 text-slate-900 text-center">Tentang <span class="text-orange-600">Ilin Catering</span></h1>
            
            <div class="bg-white p-8 sm:p-12 rounded-[2.5rem] shadow-sm border border-slate-100 transition-all hover:shadow-xl">
                
                <div class="flex flex-col md:flex-row items-center gap-8 md:gap-14">
                    
                    <div class="shrink-0">
                        <img src="{{ asset('images/logo-ilin.png') }}" alt="Ilin Catering Logo" class="w-48 md:w-64 h-auto object-contain drop-shadow-sm">
                    </div>
                    
                    {{-- Narasi Komersial Murni Ilin Catering --}}
                    <div class="leading-relaxed text-slate-600 text-base sm:text-lg space-y-4 sm:space-y-6 text-center md:text-left text-justify flex-1">
                        <p><strong>Ilin Catering</strong> merupakan salah satu penyedia jasa boga terpercaya di wilayah Bati-Bati, Tanah Laut, yang mana menu-menunya sangat dinanti oleh masyarakat di berbagai macam acara. Berdiri sejak tahun <strong>2020</strong> dan berlokasi di Desa Padang, Ilin Catering mampu menyediakan berbagai macam pilihan menu hidangan, baik dalam jumlah atau porsi besar maupun kecil, yang dapat dipesan sesuai dengan budget yang Anda miliki.</p>
                        
                        <p>Kami melayani berbagai macam kalangan dan kebutuhan acara, seperti instansi perkantoran, acara syukuran, serta arisan keluarga. Menu yang disediakan diolah dengan cita rasa otentik khas masakan rumahan tradisional hingga modern, yang didukung penuh dengan standar kebersihan yang tinggi serta kehalalan bahan baku demi menyuguhkan kualitas rasa prima.</p>
                        
                        {{-- Daftar Layanan Bullet Points --}}
                        <div class="pt-2 text-left">
                            <p class="font-black text-slate-900 text-sm sm:text-base uppercase tracking-wider mb-3">Ilin Catering menyediakan berbagai macam catering pilihan seperti:</p>
                            <ul class="list-disc pl-5 space-y-1.5 text-slate-700 text-xs sm:text-sm font-semibold">
                                <li>Paket Prasmanan Ilin Catering</li>
                                <li>Nasi Box / Nasi Kotak Instansi & Acara</li>
                                <li>Paket Snack Box / Kue Kotak Syukuran</li>
                                <li>Nasi Tumpeng & Hidangan Hantaran Acara</li>
                            </ul>
                        </div>
                        
                        <p class="pt-2">Ilin Catering akan senantiasa memberikan pelayanan dan kualitas masakan terbaik bagi Anda. Beranekaragam menu segar yang disediakan oleh Ilin Catering dapat memudahkan Anda menentukan pilihan terbaik demi menyempurnakan setiap momentum spesial Anda.</p>
                    </div>

                </div>
            </div>
            
        </div>
    </section>

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
            &copy; {{ date('Y') }} Ilin Catering. Made with ♡ by Ghina.
        </div>
    </footer>

</body>
</html>