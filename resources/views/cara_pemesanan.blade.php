<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cara Pemesanan | Ilin Catering</title>
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

    <section class="pt-32 sm:pt-40 pb-20 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full">Panduan Lengkap</span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mt-4 mb-4">Alur Mudah Pemesanan Katering 🍱</h1>
                <p class="text-slate-500 mt-3 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">Ikuti 5 tahapan praktis dari pemilihan menu lezat hingga pesanan mendarat di lokasi acara Anda.</p>
            </div>

            <div class="relative border-l-2 border-dashed border-slate-200 ml-4 sm:ml-32 space-y-12">
                
                <div class="relative pl-8 sm:pl-10">
                    <span class="absolute -left-5 sm:-left-6 top-0 bg-slate-900 text-white w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-lg shadow-md border-4 border-slate-50">1</span>
                    <div class="absolute -left-20 sm:-left-36 top-2.5 hidden sm:block text-right w-24">
                        <span class="text-sm font-black text-orange-600 uppercase tracking-wider">Langkah 01</span>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                                <i class="fa-solid fa-utensils text-orange-500 text-lg"></i>
                            </div>
                            Pilih Menu & Tentukan Porsi
                        </h3>
                        <p class="text-slate-600 text-base mt-4 leading-relaxed font-medium">
                            Masuk ke akun Anda, buka halaman katalog etalase menu kami. Gunakan tombol sakti <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">Plus/Minus (+/-)</span> langsung di halaman depan untuk menentukan kuantitas porsi kotak atau prasmanan sesuai keperluan acara Anda.
                        </p>
                    </div>
                </div>

                <div class="relative pl-8 sm:pl-10">
                    <span class="absolute -left-5 sm:-left-6 top-0 bg-slate-900 text-white w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-lg shadow-md border-4 border-slate-50">2</span>
                    <div class="absolute -left-20 sm:-left-36 top-2.5 hidden sm:block text-right w-24">
                        <span class="text-sm font-black text-orange-600 uppercase tracking-wider">Langkah 02</span>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                <i class="fa-solid fa-file-invoice text-blue-500 text-lg"></i>
                            </div>
                            Isi Rincian Acara & Jadwal
                        </h3>
                        <p class="text-slate-600 text-base mt-4 leading-relaxed font-medium">
                            Buka halaman keranjang untuk mengisi formulir pengiriman secara lengkap. Wajib memasukkan <span class="font-bold text-slate-900">Nama Penerima, Alamat Lokasi Acara, serta Tanggal & Jam Acara Hantaran</span> agar tim katering kami tidak salah waktu pengantaran.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                            {{-- 🌟 Alert Peringatan Batas Pemesanan H-3 --}}
                            <div class="bg-rose-50 border border-rose-200 p-4 sm:p-5 rounded-2xl flex gap-4 items-start shadow-sm">
                                <div class="bg-white text-rose-500 w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm text-sm">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-rose-800 text-sm uppercase tracking-wide">Batas Min. Pemesanan</h4>
                                    <p class="text-rose-700 text-xs sm:text-sm font-medium mt-1 leading-relaxed">
                                        Pemesanan harus dilakukan maksimal <strong class="font-black">H-3 (3 hari sebelum)</strong> acara untuk keperluan persiapan bahan segar dapur katering kami.
                                    </p>
                                </div>
                            </div>

                            {{-- 🌟 Alert Pembatasan Kuota Eksklusif 1 Pesanan Per Hari --}}
                            <div class="bg-amber-50 border border-amber-200 p-4 sm:p-5 rounded-2xl flex gap-4 items-start shadow-sm">
                                <div class="bg-white text-amber-500 w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm text-sm">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-amber-800 text-sm uppercase tracking-wide">Slot Eksklusif Harian</h4>
                                    <p class="text-amber-700 text-xs sm:text-sm font-medium mt-1 leading-relaxed">
                                        Demi rasa terbaik, kami membatasi <strong class="font-black">Maksimal 1 Pesanan Per Hari</strong>. Jika tanggal pilihan Anda sudah penuh, sistem otomatis mengunci tanggal tersebut.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative pl-8 sm:pl-10">
                    <span class="absolute -left-5 sm:-left-6 top-0 bg-slate-900 text-white w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-lg shadow-md border-4 border-slate-50">3</span>
                    <div class="absolute -left-20 sm:-left-36 top-2.5 hidden sm:block text-right w-24">
                        <span class="text-sm font-black text-orange-600 uppercase tracking-wider">Langkah 03</span>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                                <i class="fa-solid fa-credit-card text-purple-500 text-lg"></i>
                            </div>
                            Bayar DP Uang Muka 30%
                        </h3>
                        <p class="text-slate-600 text-base mt-4 leading-relaxed font-medium">
                            Sistem kami menggunakan pembayaran otomatis via gateway <span class="font-bold text-slate-900">Midtrans</span>. Anda diminta melunasi uang muka sebesar <span class="bg-yellow-100 text-yellow-800 font-bold px-2 py-0.5 rounded">30% dari total tagihan</span> melalui Transfer Bank, E-Wallet, atau QRIS untuk menvalidasi pesanan.
                        </p>
                    </div>
                </div>

                <div class="relative pl-8 sm:pl-10">
                    <span class="absolute -left-5 sm:-left-6 top-0 bg-slate-900 text-white w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-lg shadow-md border-4 border-slate-50">4</span>
                    <div class="absolute -left-20 sm:-left-36 top-2.5 hidden sm:block text-right w-24">
                        <span class="text-sm font-black text-orange-600 uppercase tracking-wider">Langkah 04</span>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i class="fa-regular fa-calendar-check text-emerald-500 text-lg"></i>
                            </div>
                            Penjadwalan Masak Otomatis
                        </h3>
                        <p class="text-slate-600 text-base mt-4 leading-relaxed font-medium">
                            Begitu status DP terverifikasi, sistem backend otomatis mengirim notifikasi jadwal dan alarm pengingat ke <span class="font-bold text-slate-900">Google Calendar Admin Katering tepat di H-3 dan H-1</span> acara untuk memastikan persiapan bahan masakan Anda aman terencana.
                        </p>
                    </div>
                </div>

                <div class="relative pl-8 sm:pl-10">
                    <span class="absolute -left-5 sm:-left-6 top-0 bg-orange-500 text-white w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-lg shadow-md border-4 border-slate-50">5</span>
                    <div class="absolute -left-20 sm:-left-36 top-2.5 hidden sm:block text-right w-24">
                        <span class="text-sm font-black text-orange-600 uppercase tracking-wider">Langkah 05</span>
                    </div>
                    {{-- 🌟 Warna border & shadow kotak tetap oranye agar estetik --}}
                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] border-2 border-orange-200 shadow-lg shadow-orange-100 transition hover:shadow-orange-200">
                        {{-- 🌟 JUDUL DIUBAH JADI HITAM (text-slate-900), Ikon tetap oranye --}}
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                                <i class="fa-solid fa-truck-ramp-box text-orange-600 text-lg"></i>
                            </div>
                            Hantaran Tiba & Pelunasan 70%
                        </h3>
                        <p class="text-slate-600 text-base mt-4 leading-relaxed font-medium">
                            Kurir kami akan mengantarkan pesanan katering tepat waktu ke alamat hantaran Anda. Sisa kekurangan pembayaran sebesar <span class="font-bold text-slate-900 bg-orange-100 px-2 py-0.5 rounded">70% bisa Anda lunasi secara COD (Tunai)</span> kepada kurir di lokasi, dan pesanan Anda resmi dicatat selesai.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white py-12 sm:py-16 mt-10">
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